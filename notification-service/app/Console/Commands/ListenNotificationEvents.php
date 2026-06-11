<?php

namespace App\Console\Commands;

use App\Models\NotificationLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use App\Services\EmailTemplateService;

class ListenNotificationEvents extends Command
{
    protected $signature = 'notifications:listen-redis';

    protected $description = 'Listen Redis Pub/Sub events and process notification messages';

    public function handle(): int
    {
        $this->info('Notification Redis listener started...');
        $this->info('Redis client: ' . config('database.redis.client'));
        $this->info('Redis host: ' . config('database.redis.default.host'));
        $this->info('Redis port: ' . config('database.redis.default.port'));

        try {
            $pong = Redis::ping();
            $this->info('Redis ping: ' . (string) $pong);
        } catch (\Exception $e) {
            $this->error('Redis connection failed: ' . $e->getMessage());

            return self::FAILURE;
        }

        $channels = [
            'otp_requested',
            'booking_created',
            'booking_approved',
            'booking_rejected',
            'booking_canceled',
            'member_registered',
        ];

        $this->info('Subscribing to channels: ' . implode(', ', $channels));

        try {
            Redis::connection('default')->subscribe($channels, function ($message, $channel) {
                $this->info("Received event from channel {$channel}: {$message}");

                $payload = json_decode($message, true);

                if (!is_array($payload)) {
                    $this->error("Invalid payload from channel {$channel}");

                    return;
                }

                match ($channel) {
                    'otp_requested' => $this->handleOtpRequested($payload),
                    'booking_created',
                    'booking_approved',
                    'booking_rejected',
                    'booking_canceled' => $this->handleBookingNotification($channel, $payload),
                    'member_registered' => $this->handleMemberRegistered($payload),
                    default => null,
                };
            });
        } catch (\Exception $e) {
            $this->error('Redis subscribe failed: ' . $e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    private function handleOtpRequested(array $payload): void
    {
        $email = $payload['email'] ?? null;
        $name = $payload['name'] ?? 'Member';
        $otp = $payload['otp'] ?? null;
        $expiredAt = $payload['expired_at'] ?? '10 menit';

        if (!$email || !$otp) {
            $this->error('OTP payload invalid');

            return;
        }

        $subject = 'Kode OTP Registrasi Retobluto Arena';

        $message = "Halo {$name}, kode OTP Anda adalah {$otp}. "
            . "Kode ini berlaku sampai {$expiredAt}. Jangan berikan kode ini kepada siapa pun.";

        $this->sendAndLog($email, 'otp', $subject, $message, $payload);
    }

    private function handleBookingNotification(string $channel, array $payload): void
    {
        $email = $payload['recipient_email'] ?? $payload['email'] ?? null;

        if (!$email) {
            $this->error("Booking notification payload invalid from {$channel}");

            return;
        }

        $status = str_replace('booking_', '', $channel);
        $memberName = $payload['member_name'] ?? 'Member';
        $fieldName = $payload['field_name'] ?? 'Lapangan';
        $bookingDate = $payload['booking_date'] ?? '-';
        $startTime = $payload['start_time'] ?? '-';
        $endTime = $payload['end_time'] ?? '-';

        $subject = match ($channel) {
            'booking_approved' => 'Booking Lapangan Disetujui',
            'booking_rejected' => 'Booking Lapangan Ditolak',
            'booking_canceled' => 'Booking Lapangan Dibatalkan',
            default => 'Booking Lapangan Dibuat',
        };

        $message = "Halo {$memberName}, status booking Anda untuk {$fieldName} "
            . "pada tanggal {$bookingDate} pukul {$startTime} - {$endTime} "
            . "adalah {$status}.";

        $this->sendAndLog($email, $channel, $subject, $message, $payload);
    }

    private function handleMemberRegistered(array $payload): void
    {
        $email = $payload['email'] ?? null;
        $name = $payload['name'] ?? 'Member';

        if (!$email) {
            $this->error('Member registered payload invalid');

            return;
        }

        $subject = 'Registrasi Retobluto Arena Berhasil';

        $message = "Halo {$name}, akun member Retobluto Arena Anda berhasil dibuat.";

        $this->sendAndLog($email, 'member_registered', $subject, $message, $payload);
    }

    private function sendAndLog(
        string $recipientEmail,
        string $type,
        string $subject,
        string $message,
        array $payload = []
    ): void {
        try {
            $html = app(EmailTemplateService::class)->render(
                type: $type,
                subject: $subject,
                message: $message,
                payload: $payload
            );

            Mail::send([], [], function ($mail) use ($recipientEmail, $subject, $html) {
                $mail->to($recipientEmail)
                    ->subject($subject)
                    ->html($html);
            });

            NotificationLog::create([
                'recipient_email' => $recipientEmail,
                'type' => $type,
                'subject' => $subject,
                'message' => $message,
                'status' => 'sent',
                'payload' => $payload,
                'sent_at' => now(),
                'error_message' => null,
            ]);

            $this->info("Notification sent to {$recipientEmail} type {$type}");
        } catch (\Exception $e) {
            NotificationLog::create([
                'recipient_email' => $recipientEmail,
                'type' => $type,
                'subject' => $subject,
                'message' => $message,
                'status' => 'failed',
                'payload' => $payload,
                'sent_at' => null,
                'error_message' => $e->getMessage(),
            ]);

            $this->error("Notification failed to {$recipientEmail}: {$e->getMessage()}");
        }
    }
}

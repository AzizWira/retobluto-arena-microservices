<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NotificationLog;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use App\Services\EmailTemplateService;

class NotificationController extends Controller
{
    private array $allowedTypes = [
        'otp',
        'email',
        'booking_created',
        'booking_approved',
        'booking_rejected',
        'booking_canceled',
        'member_registered',
    ];

    private array $allowedStatuses = [
        'sent',
        'failed',
    ];

    public function logs(Request $request)
    {
        $adminCheck = $this->ensureAdmin($request);

        if ($adminCheck !== true) {
            return $adminCheck;
        }

        try {
            $request->validate([
                'type' => ['nullable', Rule::in($this->allowedTypes)],
                'status' => ['nullable', Rule::in($this->allowedStatuses)],
                'search' => 'nullable|string|max:100',
            ]);

            $query = NotificationLog::query();

            if ($request->filled('type')) {
                $query->where('type', $request->query('type'));
            }

            if ($request->filled('status')) {
                $query->where('status', $request->query('status'));
            }

            if ($request->filled('search')) {
                $search = $request->query('search');

                $query->where(function ($q) use ($search) {
                    $q->where('recipient_email', 'like', "%{$search}%")
                        ->orWhere('subject', 'like', "%{$search}%")
                        ->orWhere('message', 'like', "%{$search}%");
                });
            }

            $logs = $query->latest()->get();

            return response()->json([
                'success' => true,
                'message' => 'Log notifikasi berhasil diambil',
                'data' => $logs,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi filter gagal',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil log notifikasi',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function showLog(Request $request, string $id)
    {
        $adminCheck = $this->ensureAdmin($request);

        if ($adminCheck !== true) {
            return $adminCheck;
        }

        $log = NotificationLog::find($id);

        if (!$log) {
            return response()->json([
                'success' => false,
                'message' => 'Log notifikasi tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail log notifikasi berhasil diambil',
            'data' => $log,
        ]);
    }

    public function sendEmail(Request $request)
    {
        $adminCheck = $this->ensureAdmin($request);

        if ($adminCheck !== true) {
            return $adminCheck;
        }

        try {
            $validated = $request->validate([
                'recipient_email' => 'required|email|max:150',
                'subject' => 'required|string|max:255',
                'message' => 'required|string',
            ]);

            $log = $this->sendAndLog(
                recipientEmail: $validated['recipient_email'],
                type: 'email',
                subject: $validated['subject'],
                message: $validated['message'],
                payload: $validated
            );

            return response()->json([
                'success' => $log->status === 'sent',
                'message' => $log->status === 'sent'
                    ? 'Email berhasil diproses'
                    : 'Email gagal diproses',
                'data' => $log,
            ], $log->status === 'sent' ? 200 : 500);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function sendOtp(Request $request)
    {
        $adminCheck = $this->ensureAdmin($request);

        if ($adminCheck !== true) {
            return $adminCheck;
        }

        return $this->processOtpNotification($request);
    }

    public function internalOtp(Request $request)
    {
        $internalCheck = $this->ensureInternalService($request);

        if ($internalCheck !== true) {
            return $internalCheck;
        }

        return $this->processOtpNotification($request);
    }

    public function internalBookingStatus(Request $request)
    {
        $internalCheck = $this->ensureInternalService($request);

        if ($internalCheck !== true) {
            return $internalCheck;
        }

        try {
            $validated = $request->validate([
                'recipient_email' => 'required|email|max:150',
                'member_name' => 'nullable|string|max:100',
                'field_name' => 'required|string|max:150',
                'booking_date' => 'required|date_format:Y-m-d',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i',
                'status' => ['required', Rule::in(['pending', 'approved', 'rejected', 'canceled'])],
                'booking_id' => 'nullable|integer',
            ]);

            $type = 'booking_' . $validated['status'];

            $subject = match ($validated['status']) {
                'approved' => 'Booking Lapangan Disetujui',
                'rejected' => 'Booking Lapangan Ditolak',
                'canceled' => 'Booking Lapangan Dibatalkan',
                default => 'Booking Lapangan Dibuat',
            };

            $memberName = $validated['member_name'] ?? 'Member';

            $message = "Halo {$memberName}, status booking Anda untuk {$validated['field_name']} "
                . "pada tanggal {$validated['booking_date']} pukul {$validated['start_time']} - {$validated['end_time']} "
                . "adalah {$validated['status']}.";

            $log = $this->sendAndLog(
                recipientEmail: $validated['recipient_email'],
                type: $type,
                subject: $subject,
                message: $message,
                payload: $validated
            );

            return response()->json([
                'success' => $log->status === 'sent',
                'message' => $log->status === 'sent'
                    ? 'Notifikasi status booking berhasil diproses'
                    : 'Notifikasi status booking gagal diproses',
                'data' => $log,
            ], $log->status === 'sent' ? 200 : 500);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    private function processOtpNotification(Request $request)
    {
        try {
            $validated = $request->validate([
                'recipient_email' => 'required_without:email|email|max:150',
                'email' => 'required_without:recipient_email|email|max:150',
                'name' => 'nullable|string|max:100',
                'otp' => 'required|string|size:6',
                'expired_at' => 'nullable|string',
            ]);

            $recipientEmail = $validated['recipient_email'] ?? $validated['email'];
            $name = $validated['name'] ?? 'Member';
            $expiredAt = $validated['expired_at'] ?? '10 menit';

            $subject = 'Kode OTP Registrasi Retobluto Arena';

            $message = "Halo {$name}, kode OTP Anda adalah {$validated['otp']}. "
                . "Kode ini berlaku sampai {$expiredAt}. Jangan berikan kode ini kepada siapa pun.";

            $log = $this->sendAndLog(
                recipientEmail: $recipientEmail,
                type: 'otp',
                subject: $subject,
                message: $message,
                payload: $validated
            );

            return response()->json([
                'success' => $log->status === 'sent',
                'message' => $log->status === 'sent'
                    ? 'OTP berhasil diproses'
                    : 'OTP gagal diproses',
                'data' => $log,
            ], $log->status === 'sent' ? 200 : 500);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    private function sendAndLog(
        string $recipientEmail,
        string $type,
        string $subject,
        string $message,
        array $payload = []
    ): NotificationLog {
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

            return NotificationLog::create([
                'recipient_email' => $recipientEmail,
                'type' => $type,
                'subject' => $subject,
                'message' => $message,
                'status' => 'sent',
                'payload' => $payload,
                'sent_at' => now(),
                'error_message' => null,
            ]);
        } catch (\Exception $e) {
            return NotificationLog::create([
                'recipient_email' => $recipientEmail,
                'type' => $type,
                'subject' => $subject,
                'message' => $message,
                'status' => 'failed',
                'payload' => $payload,
                'sent_at' => null,
                'error_message' => $e->getMessage(),
            ]);
        }
    }

    private function ensureAuthenticated(Request $request)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak ditemukan',
            ], 401);
        }

        try {
            $authServiceUrl = rtrim(env('AUTH_SERVICE_URL', 'http://auth-service:8000'), '/');

            $response = Http::timeout(15)
                ->connectTimeout(5)
                ->retry(2, 300)
                ->withToken($token)
                ->acceptJson()
                ->post($authServiceUrl . '/api/validate-token');

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token tidak valid',
                    'auth_service_status' => $response->status(),
                    'auth_service_response' => $response->json(),
                ], 401);
            }

            $payload = $response->json();

            if (!($payload['valid'] ?? false) || !isset($payload['user'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token tidak valid',
                ], 401);
            }

            return [
                'payload' => $payload,
                'user' => $payload['user'],
            ];
        } catch (ConnectionException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Auth Service unavailable',
                'error' => $e->getMessage(),
            ], 503);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memvalidasi token',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function ensureAdmin(Request $request)
    {
        $authCheck = $this->ensureAuthenticated($request);

        if (!is_array($authCheck)) {
            return $authCheck;
        }

        $user = $authCheck['user'];

        if (($user['role'] ?? null) !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Endpoint ini hanya untuk admin.',
                'user_role' => $user['role'] ?? null,
            ], 403);
        }

        return true;
    }

    private function ensureInternalService(Request $request)
    {
        $secret = $request->header('X-INTERNAL-SECRET');

        if (!$secret || $secret !== env('INTERNAL_SERVICE_SECRET')) {
            return response()->json([
                'success' => false,
                'message' => 'Akses internal service ditolak.',
            ], 403);
        }

        return true;
    }
}

<?php

namespace App\Services;

class EmailTemplateService
{
    public function render(
        string $type,
        string $subject,
        string $message,
        array $payload = []
    ): string {
        $brand = [
            'name' => 'ARENALO',
            'full_name' => 'Retobluto Arena',
            'tagline' => 'Sistem Booking Lapangan',
            'support_email' => env('MAIL_FROM_ADDRESS', 'noreply@retobluto.test'),
        ];

        if ($type === 'otp') {
            return view('emails.otp', [
                'brand' => $brand,
                'subject' => $subject,
                'name' => $payload['name'] ?? 'Member',
                'otp' => $payload['otp'] ?? '------',
                'expiredAt' => $payload['expired_at'] ?? '10 menit',
                'message' => $message,
            ])->render();
        }

        return view('emails.notification', [
            'brand' => $brand,
            'type' => $type,
            'typeLabel' => $this->typeLabel($type),
            'subject' => $subject,
            'message' => $message,
            'recipientName' => $payload['member_name'] ?? $payload['name'] ?? 'Member',
            'details' => $this->details($type, $payload),
        ])->render();
    }

    private function typeLabel(string $type): string
    {
        return match ($type) {
            'email' => 'Pesan Admin',
            'booking_created' => 'Booking Dibuat',
            'booking_approved' => 'Booking Disetujui',
            'booking_rejected' => 'Booking Ditolak',
            'booking_canceled' => 'Booking Dibatalkan',
            'member_registered' => 'Registrasi Member',
            default => 'Notifikasi',
        };
    }

    private function details(string $type, array $payload): array
    {
        if (str_starts_with($type, 'booking_')) {
            return [
                ['label' => 'Lapangan', 'value' => $payload['field_name'] ?? '-'],
                ['label' => 'Tanggal', 'value' => $payload['booking_date'] ?? '-'],
                ['label' => 'Jam', 'value' => ($payload['start_time'] ?? '-') . ' - ' . ($payload['end_time'] ?? '-')],
                ['label' => 'Status', 'value' => $payload['status'] ?? str_replace('booking_', '', $type)],
            ];
        }

        if ($type === 'member_registered') {
            return [
                ['label' => 'Nama', 'value' => $payload['name'] ?? '-'],
                ['label' => 'Email', 'value' => $payload['email'] ?? '-'],
            ];
        }

        return [];
    }
}

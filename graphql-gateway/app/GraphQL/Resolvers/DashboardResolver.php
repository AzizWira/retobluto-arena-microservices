<?php

namespace App\GraphQL\Resolvers;

use App\GraphQL\Services\BookingServiceClient;
use App\GraphQL\Services\FieldServiceClient;
use App\GraphQL\Services\MemberServiceClient;
use App\GraphQL\Services\NotificationServiceClient;

class DashboardResolver
{
    public function __construct(
        private readonly FieldServiceClient $fieldService,
        private readonly MemberServiceClient $memberService,
        private readonly BookingServiceClient $bookingService,
        private readonly NotificationServiceClient $notificationService
    ) {}

    public function summary(array $context): array
    {
        $authorization = $context['authorization'] ?? null;

        $errors = [];

        $fields = $this->fieldService->all();

        if (!$authorization) {
            $errors['authorization'][] = 'Token admin diperlukan untuk menghitung member, booking, dan notifikasi.';
        }

        $members = [];
        $bookings = [];
        $logs = [];

        if ($authorization) {
            $memberResult = $this->memberService->all([], $authorization);
            $bookingResult = $this->bookingService->all([], $authorization);
            $notificationResult = $this->notificationService->logs([], $authorization);

            $members = is_array($memberResult['data'] ?? null) ? $memberResult['data'] : [];
            $bookings = is_array($bookingResult['data'] ?? null) ? $bookingResult['data'] : [];
            $logs = is_array($notificationResult['data'] ?? null) ? $notificationResult['data'] : [];

            if (!($memberResult['success'] ?? false)) {
                $errors['member_service'][] = $memberResult['message'] ?? 'Gagal mengambil data member.';
            }

            if (!($bookingResult['success'] ?? false)) {
                $errors['booking_service'][] = $bookingResult['message'] ?? 'Gagal mengambil data booking.';
            }

            if (!($notificationResult['success'] ?? false)) {
                $errors['notification_service'][] = $notificationResult['message'] ?? 'Gagal mengambil log notifikasi.';
            }
        }

        return [
            'success' => empty($errors),
            'message' => empty($errors)
                ? 'Dashboard summary berhasil dihitung.'
                : 'Dashboard summary dihitung sebagian.',
            'fields_total' => count($fields),
            'fields_available' => $this->countBy($fields, 'status', 'available'),
            'fields_maintenance' => $this->countBy($fields, 'status', 'maintenance'),
            'fields_inactive' => $this->countBy($fields, 'status', 'inactive'),

            'members_total' => count($members),
            'members_active' => $this->countBy($members, 'status', 'active'),
            'members_inactive' => $this->countBy($members, 'status', 'inactive'),
            'members_blocked' => $this->countBy($members, 'status', 'blocked'),

            'bookings_total' => count($bookings),
            'bookings_pending' => $this->countBy($bookings, 'status', 'pending'),
            'bookings_approved' => $this->countBy($bookings, 'status', 'approved'),
            'bookings_rejected' => $this->countBy($bookings, 'status', 'rejected'),
            'bookings_canceled' => $this->countBy($bookings, 'status', 'canceled'),

            'notifications_total' => count($logs),
            'notifications_sent' => $this->countBy($logs, 'status', 'sent'),
            'notifications_failed' => $this->countBy($logs, 'status', 'failed'),

            'errors' => empty($errors) ? null : $errors,
        ];
    }

    private function countBy(array $items, string $key, string $value): int
    {
        return collect($items)
            ->filter(fn($item) => is_array($item) && ($item[$key] ?? null) === $value)
            ->count();
    }
}

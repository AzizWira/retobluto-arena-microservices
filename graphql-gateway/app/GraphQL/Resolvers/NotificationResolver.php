<?php

namespace App\GraphQL\Resolvers;

use App\GraphQL\Core\GraphQLParser;
use App\GraphQL\Services\NotificationServiceClient;

class NotificationResolver
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

    public function __construct(
        private readonly NotificationServiceClient $notificationService
    ) {}

    public function logs(string $query, array $context): array
    {
        $args = GraphQLParser::args($query, 'notificationLogs');

        $errors = [];

        if (empty($context['authorization'])) {
            $errors['authorization'][] = 'Token admin wajib diisi untuk melihat log notifikasi.';
        }

        if (isset($args['type']) && !in_array($args['type'], $this->allowedTypes, true)) {
            $errors['type'][] = 'Type notifikasi hanya boleh: ' . implode(', ', $this->allowedTypes) . '.';
        }

        if (isset($args['status']) && !in_array($args['status'], $this->allowedStatuses, true)) {
            $errors['status'][] = 'Status notifikasi hanya boleh: ' . implode(', ', $this->allowedStatuses) . '.';
        }

        if (isset($args['search']) && strlen((string) $args['search']) > 100) {
            $errors['search'][] = 'Search maksimal 100 karakter.';
        }

        if (!empty($errors)) {
            return $this->listError($errors);
        }

        $result = $this->notificationService->logs([
            'type' => $args['type'] ?? null,
            'status' => $args['status'] ?? null,
            'search' => $args['search'] ?? null,
        ], $context['authorization']);

        return [
            'success' => (bool) ($result['success'] ?? false),
            'message' => $result['message'] ?? 'Request log notifikasi selesai.',
            'logs' => is_array($result['data'] ?? null) ? $result['data'] : [],
            'errors' => $result['errors'] ?? null,
        ];
    }

    public function log(string $query, array $context): array
    {
        $args = GraphQLParser::args($query, 'notificationLog');

        $errors = [];

        if (empty($context['authorization'])) {
            $errors['authorization'][] = 'Token admin wajib diisi untuk melihat detail log notifikasi.';
        }

        if (!$this->validPositiveId($args['id'] ?? null)) {
            $errors['id'][] = 'Argument id wajib berupa angka lebih dari 0.';
        }

        if (!empty($errors)) {
            return $this->singleError($errors);
        }

        $result = $this->notificationService->log($args['id'], $context['authorization']);

        return [
            'success' => (bool) ($result['success'] ?? false),
            'message' => $result['message'] ?? 'Request detail log notifikasi selesai.',
            'log' => is_array($result['data'] ?? null) ? $result['data'] : null,
            'errors' => $result['errors'] ?? null,
        ];
    }

    private function validPositiveId(mixed $value): bool
    {
        return is_numeric($value) && (int) $value > 0;
    }

    private function listError(array $errors): array
    {
        return [
            'success' => false,
            'message' => 'Validasi GraphQL Gateway gagal.',
            'logs' => [],
            'errors' => $errors,
        ];
    }

    private function singleError(array $errors): array
    {
        return [
            'success' => false,
            'message' => 'Validasi GraphQL Gateway gagal.',
            'log' => null,
            'errors' => $errors,
        ];
    }
}

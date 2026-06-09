<?php

namespace App\GraphQL\Resolvers;

use App\GraphQL\Core\GraphQLParser;
use App\GraphQL\Services\FieldServiceClient;

class FieldResolver
{
    private array $allowedTypes = [
        'Futsal',
        'Badminton',
        'Basket',
        'Tenis',
        'Mini Soccer',
        'Voli',
    ];

    private array $allowedStatuses = [
        'available',
        'maintenance',
        'inactive',
    ];

    public function __construct(
        private readonly FieldServiceClient $fieldService
    ) {}

    public function fields(string $query): array
    {
        $args = GraphQLParser::args($query, 'fields');

        return $this->fieldService->all([
            'type' => $args['type'] ?? null,
            'status' => $args['status'] ?? null,
            'search' => $args['search'] ?? null,
        ]);
    }

    public function availableFields(): array
    {
        return $this->fieldService->available();
    }

    public function field(string $query): ?array
    {
        $args = GraphQLParser::args($query, 'field');

        if (!isset($args['id'])) {
            return null;
        }

        return $this->fieldService->find($args['id']);
    }

    public function fieldSchedule(string $query): array
    {
        $args = GraphQLParser::args($query, 'fieldSchedule');

        if (!isset($args['id'])) {
            return [
                'success' => false,
                'message' => 'Argument id wajib diisi.',
            ];
        }

        return $this->fieldService->schedule(
            id: $args['id'],
            date: $args['date'] ?? null
        );
    }

    public function createField(string $query, array $context): array
    {
        $args = GraphQLParser::args($query, 'createField');

        $validation = $this->validateFieldPayload($args, false);

        if ($validation !== true) {
            return $validation;
        }

        $result = $this->fieldService->create([
            'name' => $args['name'],
            'type' => $args['type'],
            'description' => $args['description'] ?? null,
            'location' => $args['location'] ?? null,
            'price_per_hour' => $args['price_per_hour'],
            'status' => $args['status'] ?? 'available',
            'open_time' => $args['open_time'] ?? null,
            'close_time' => $args['close_time'] ?? null,
        ], $context['authorization'] ?? null);

        return $this->fieldMutationPayload($result, 'Gagal membuat lapangan.');
    }

    public function updateField(string $query, array $context): array
    {
        $args = GraphQLParser::args($query, 'updateField');

        if (!isset($args['id'])) {
            return $this->validationError([
                'id' => ['Argument id wajib diisi.'],
            ]);
        }

        $validation = $this->validateFieldPayload($args, true);

        if ($validation !== true) {
            return $validation;
        }

        $result = $this->fieldService->update($args['id'], [
            'name' => $args['name'],
            'type' => $args['type'],
            'description' => $args['description'] ?? null,
            'location' => $args['location'] ?? null,
            'price_per_hour' => $args['price_per_hour'],
            'status' => $args['status'],
            'open_time' => $args['open_time'] ?? null,
            'close_time' => $args['close_time'] ?? null,
        ], $context['authorization'] ?? null);

        return $this->fieldMutationPayload($result, 'Gagal memperbarui lapangan.');
    }

    public function updateFieldStatus(string $query, array $context): array
    {
        $args = GraphQLParser::args($query, 'updateFieldStatus');

        $errors = [];

        if (!isset($args['id'])) {
            $errors['id'][] = 'Argument id wajib diisi.';
        }

        if (!isset($args['status'])) {
            $errors['status'][] = 'Argument status wajib diisi.';
        } elseif (!in_array($args['status'], $this->allowedStatuses, true)) {
            $errors['status'][] = 'Status hanya boleh: ' . implode(', ', $this->allowedStatuses) . '.';
        }

        if (!empty($errors)) {
            return $this->validationError($errors);
        }

        $result = $this->fieldService->updateStatus(
            id: $args['id'],
            status: $args['status'],
            authorization: $context['authorization'] ?? null
        );

        return $this->fieldMutationPayload($result, 'Gagal memperbarui status lapangan.');
    }

    public function deleteField(string $query, array $context): array
    {
        $args = GraphQLParser::args($query, 'deleteField');

        if (!isset($args['id'])) {
            return $this->validationError([
                'id' => ['Argument id wajib diisi.'],
            ]);
        }

        $result = $this->fieldService->delete(
            id: $args['id'],
            authorization: $context['authorization'] ?? null
        );

        return $this->fieldMutationPayload($result, 'Gagal menghapus lapangan.');
    }

    private function validateFieldPayload(array $args, bool $isUpdate): bool|array
    {
        $errors = [];

        if (empty($args['name'])) {
            $errors['name'][] = 'Nama lapangan wajib diisi.';
        } elseif (strlen((string) $args['name']) > 150) {
            $errors['name'][] = 'Nama lapangan maksimal 150 karakter.';
        }

        if (empty($args['type'])) {
            $errors['type'][] = 'Tipe lapangan wajib diisi.';
        } elseif (!in_array($args['type'], $this->allowedTypes, true)) {
            $errors['type'][] = 'Tipe lapangan hanya boleh: ' . implode(', ', $this->allowedTypes) . '.';
        }

        if (!isset($args['price_per_hour'])) {
            $errors['price_per_hour'][] = 'Harga per jam wajib diisi.';
        } elseif (!is_numeric($args['price_per_hour']) || (float) $args['price_per_hour'] < 0) {
            $errors['price_per_hour'][] = 'Harga per jam harus berupa angka minimal 0.';
        }

        if ($isUpdate) {
            if (empty($args['status'])) {
                $errors['status'][] = 'Status wajib diisi.';
            } elseif (!in_array($args['status'], $this->allowedStatuses, true)) {
                $errors['status'][] = 'Status hanya boleh: ' . implode(', ', $this->allowedStatuses) . '.';
            }
        } elseif (isset($args['status']) && !in_array($args['status'], $this->allowedStatuses, true)) {
            $errors['status'][] = 'Status hanya boleh: ' . implode(', ', $this->allowedStatuses) . '.';
        }

        if (isset($args['open_time']) && !$this->validTime($args['open_time'])) {
            $errors['open_time'][] = 'Jam buka harus berformat HH:mm, contoh 08:00.';
        }

        if (isset($args['close_time']) && !$this->validTime($args['close_time'])) {
            $errors['close_time'][] = 'Jam tutup harus berformat HH:mm, contoh 22:00.';
        }

        if (
            isset($args['open_time'], $args['close_time'])
            && $this->validTime($args['open_time'])
            && $this->validTime($args['close_time'])
            && $args['close_time'] <= $args['open_time']
        ) {
            $errors['close_time'][] = 'Jam tutup harus lebih besar dari jam buka.';
        }

        if (!empty($errors)) {
            return $this->validationError($errors);
        }

        return true;
    }

    private function validTime(string $time): bool
    {
        return preg_match('/^\d{2}:\d{2}$/', $time) === 1;
    }

    private function validationError(array $errors): array
    {
        return [
            'success' => false,
            'message' => 'Validasi GraphQL Gateway gagal.',
            'field' => null,
            'errors' => $errors,
        ];
    }

    private function fieldMutationPayload(array $result, string $fallbackMessage): array
    {
        return [
            'success' => (bool) ($result['success'] ?? false),
            'message' => $result['message'] ?? $fallbackMessage,
            'field' => $result['data'] ?? null,
            'errors' => $result['errors'] ?? null,
        ];
    }
}

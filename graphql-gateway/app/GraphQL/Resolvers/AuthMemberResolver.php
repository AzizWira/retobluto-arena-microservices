<?php

namespace App\GraphQL\Resolvers;

use App\GraphQL\Core\GraphQLParser;
use App\GraphQL\Services\AuthServiceClient;
use App\GraphQL\Services\MemberServiceClient;

class AuthMemberResolver
{
    private array $allowedStatuses = [
        'active',
        'inactive',
        'blocked',
    ];

    public function __construct(
        private readonly AuthServiceClient $authService,
        private readonly MemberServiceClient $memberService
    ) {}

    public function me(array $context): array
    {
        $result = $this->authService->me($context['authorization'] ?? null);

        return [
            'success' => (bool) ($result['success'] ?? false),
            'message' => $result['message'] ?? 'Request selesai.',
            'valid' => null,
            'user' => $result['data'] ?? null,
            'data' => $result['data'] ?? null,
            'errors' => $result['errors'] ?? null,
        ];
    }

    public function validateToken(array $context): array
    {
        $result = $this->authService->validateToken($context['authorization'] ?? null);

        return [
            'success' => (bool) ($result['success'] ?? false),
            'message' => $result['message'] ?? 'Validasi token selesai.',
            'valid' => (bool) ($result['valid'] ?? false),
            'user' => $result['data']['user'] ?? null,
            'data' => $result['data'] ?? null,
            'errors' => $result['errors'] ?? null,
        ];
    }

    public function adminCreateMember(string $query, array $context): array
    {
        $args = GraphQLParser::args($query, 'adminCreateMember');

        $validation = $this->validateAdminCreateMember($args, $context);

        if ($validation !== true) {
            return [
                'success' => false,
                'message' => 'Validasi GraphQL Gateway gagal.',
                'user' => null,
                'member' => null,
                'data' => null,
                'errors' => $validation,
            ];
        }

        $result = $this->authService->adminCreateMember([
            'name' => $args['name'],
            'email' => $args['email'],
            'password' => $args['password'],
            'phone' => $args['phone'] ?? null,
            'address' => $args['address'] ?? null,
        ], $context['authorization']);

        $data = $result['data'] ?? [];

        return [
            'success' => (bool) ($result['success'] ?? false),
            'message' => $result['message'] ?? 'Request selesai.',
            'user' => $data['user'] ?? null,
            'member' => $data['member_sync']['data'] ?? null,
            'data' => $data,
            'errors' => $result['errors'] ?? null,
        ];
    }

    public function deleteMemberAuthAccount(string $query, array $context): array
    {
        $args = GraphQLParser::args($query, 'deleteMemberAuthAccount');

        $errors = [];

        if (empty($context['authorization'])) {
            $errors['authorization'][] = 'Token admin wajib diisi.';
        }

        if (empty($args['user_id']) && empty($args['email'])) {
            $errors['user_id'][] = 'user_id atau email wajib diisi.';
            $errors['email'][] = 'user_id atau email wajib diisi.';
        }

        if (!empty($args['user_id']) && !$this->validPositiveId($args['user_id'])) {
            $errors['user_id'][] = 'user_id harus berupa angka lebih dari 0.';
        }

        if (!empty($args['email']) && !filter_var($args['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'][] = 'Email tidak valid.';
        }

        if (!empty($errors)) {
            return [
                'success' => false,
                'message' => 'Validasi GraphQL Gateway gagal.',
                'valid' => false,
                'user' => null,
                'data' => null,
                'errors' => $errors,
            ];
        }

        $result = $this->authService->deleteMemberAuthAccount([
            'user_id' => $args['user_id'] ?? null,
            'email' => $args['email'] ?? null,
        ], $context['authorization']);

        return [
            'success' => (bool) ($result['success'] ?? false),
            'message' => $result['message'] ?? 'Request selesai.',
            'valid' => null,
            'user' => null,
            'data' => $result['data'] ?? null,
            'errors' => $result['errors'] ?? null,
        ];
    }

    public function members(string $query, array $context): array
    {
        $args = GraphQLParser::args($query, 'members');

        $errors = [];

        if (empty($context['authorization'])) {
            $errors['authorization'][] = 'Token admin wajib diisi untuk melihat data member.';
        }

        if (isset($args['status']) && !in_array($args['status'], $this->allowedStatuses, true)) {
            $errors['status'][] = 'Status member hanya boleh: ' . implode(', ', $this->allowedStatuses) . '.';
        }

        if (isset($args['search']) && strlen((string) $args['search']) > 100) {
            $errors['search'][] = 'Search maksimal 100 karakter.';
        }

        if (!empty($errors)) {
            return $this->memberListError($errors);
        }

        $result = $this->memberService->all([
            'status' => $args['status'] ?? null,
            'search' => $args['search'] ?? null,
        ], $context['authorization']);

        return $this->memberListPayload($result);
    }

    public function member(string $query, array $context): array
    {
        $args = GraphQLParser::args($query, 'member');

        $errors = [];

        if (empty($context['authorization'])) {
            $errors['authorization'][] = 'Token wajib diisi.';
        }

        if (!$this->validPositiveId($args['id'] ?? null)) {
            $errors['id'][] = 'Argument id wajib berupa angka lebih dari 0.';
        }

        if (!empty($errors)) {
            return $this->memberSingleError($errors);
        }

        $result = $this->memberService->find($args['id'], $context['authorization']);

        return $this->memberSinglePayload($result);
    }

    public function memberByUserId(string $query, array $context): array
    {
        $args = GraphQLParser::args($query, 'memberByUserId');

        $errors = [];

        if (empty($context['authorization'])) {
            $errors['authorization'][] = 'Token wajib diisi.';
        }

        if (!$this->validPositiveId($args['user_id'] ?? null)) {
            $errors['user_id'][] = 'Argument user_id wajib berupa angka lebih dari 0.';
        }

        if (!empty($errors)) {
            return $this->memberSingleError($errors);
        }

        $result = $this->memberService->byUserId($args['user_id'], $context['authorization']);

        return $this->memberSinglePayload($result);
    }

    public function myProfile(array $context): array
    {
        if (empty($context['authorization'])) {
            return $this->memberSingleError([
                'authorization' => ['Token member wajib diisi.'],
            ]);
        }

        $result = $this->memberService->profile($context['authorization']);

        return $this->memberSinglePayload($result);
    }

    public function updateProfile(string $query, array $context): array
    {
        $args = GraphQLParser::args($query, 'updateProfile');

        $errors = [];

        if (empty($context['authorization'])) {
            $errors['authorization'][] = 'Token member wajib diisi.';
        }

        if (isset($args['name']) && strlen((string) $args['name']) > 100) {
            $errors['name'][] = 'Nama maksimal 100 karakter.';
        }

        if (isset($args['phone']) && strlen((string) $args['phone']) > 20) {
            $errors['phone'][] = 'Nomor HP maksimal 20 karakter.';
        }

        if (!empty($errors)) {
            return $this->memberMutationError($errors);
        }

        $payload = [];

        foreach (['name', 'phone', 'address'] as $field) {
            if (array_key_exists($field, $args)) {
                $payload[$field] = $args[$field];
            }
        }

        $result = $this->memberService->updateProfile($payload, $context['authorization']);

        return $this->memberMutationPayload($result);
    }

    public function updateMember(string $query, array $context): array
    {
        $args = GraphQLParser::args($query, 'updateMember');

        $validation = $this->validateUpdateMember($args, $context);

        if ($validation !== true) {
            return $this->memberMutationError($validation);
        }

        $payload = [
            'user_id' => $args['user_id'] ?? null,
            'name' => $args['name'],
            'email' => $args['email'],
            'phone' => $args['phone'] ?? null,
            'address' => $args['address'] ?? null,
            'status' => $args['status'],
        ];

        $result = $this->memberService->update($args['id'], $payload, $context['authorization']);

        return $this->memberMutationPayload($result);
    }

    public function updateMemberStatus(string $query, array $context): array
    {
        $args = GraphQLParser::args($query, 'updateMemberStatus');

        $errors = [];

        if (empty($context['authorization'])) {
            $errors['authorization'][] = 'Token admin wajib diisi.';
        }

        if (!$this->validPositiveId($args['id'] ?? null)) {
            $errors['id'][] = 'Argument id wajib berupa angka lebih dari 0.';
        }

        if (empty($args['status'])) {
            $errors['status'][] = 'Status wajib diisi.';
        } elseif (!in_array($args['status'], $this->allowedStatuses, true)) {
            $errors['status'][] = 'Status member hanya boleh: ' . implode(', ', $this->allowedStatuses) . '.';
        }

        if (!empty($errors)) {
            return $this->memberMutationError($errors);
        }

        $result = $this->memberService->updateStatus(
            id: $args['id'],
            status: $args['status'],
            authorization: $context['authorization']
        );

        return $this->memberMutationPayload($result);
    }

    public function deleteMember(string $query, array $context): array
    {
        $args = GraphQLParser::args($query, 'deleteMember');

        $errors = [];

        if (empty($context['authorization'])) {
            $errors['authorization'][] = 'Token admin wajib diisi.';
        }

        if (!$this->validPositiveId($args['id'] ?? null)) {
            $errors['id'][] = 'Argument id wajib berupa angka lebih dari 0.';
        }

        if (!empty($errors)) {
            return $this->memberMutationError($errors);
        }

        $result = $this->memberService->delete($args['id'], $context['authorization']);

        return $this->memberMutationPayload($result);
    }

    private function validateAdminCreateMember(array $args, array $context): bool|array
    {
        $errors = [];

        if (empty($context['authorization'])) {
            $errors['authorization'][] = 'Token admin wajib diisi.';
        }

        if (empty($args['name'])) {
            $errors['name'][] = 'Nama member wajib diisi.';
        } elseif (strlen((string) $args['name']) > 100) {
            $errors['name'][] = 'Nama maksimal 100 karakter.';
        }

        if (empty($args['email'])) {
            $errors['email'][] = 'Email wajib diisi.';
        } elseif (!filter_var($args['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'][] = 'Email tidak valid.';
        }

        if (empty($args['password'])) {
            $errors['password'][] = 'Password wajib diisi.';
        } elseif (strlen((string) $args['password']) < 6) {
            $errors['password'][] = 'Password minimal 6 karakter.';
        }

        if (isset($args['phone']) && strlen((string) $args['phone']) > 20) {
            $errors['phone'][] = 'Nomor HP maksimal 20 karakter.';
        }

        return empty($errors) ? true : $errors;
    }

    private function validateUpdateMember(array $args, array $context): bool|array
    {
        $errors = [];

        if (empty($context['authorization'])) {
            $errors['authorization'][] = 'Token admin wajib diisi.';
        }

        if (!$this->validPositiveId($args['id'] ?? null)) {
            $errors['id'][] = 'Argument id wajib berupa angka lebih dari 0.';
        }

        if (isset($args['user_id']) && !$this->validPositiveId($args['user_id'])) {
            $errors['user_id'][] = 'user_id harus berupa angka lebih dari 0.';
        }

        if (empty($args['name'])) {
            $errors['name'][] = 'Nama wajib diisi.';
        } elseif (strlen((string) $args['name']) > 100) {
            $errors['name'][] = 'Nama maksimal 100 karakter.';
        }

        if (empty($args['email'])) {
            $errors['email'][] = 'Email wajib diisi.';
        } elseif (!filter_var($args['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'][] = 'Email tidak valid.';
        }

        if (isset($args['phone']) && strlen((string) $args['phone']) > 20) {
            $errors['phone'][] = 'Nomor HP maksimal 20 karakter.';
        }

        if (empty($args['status'])) {
            $errors['status'][] = 'Status wajib diisi.';
        } elseif (!in_array($args['status'], $this->allowedStatuses, true)) {
            $errors['status'][] = 'Status member hanya boleh: ' . implode(', ', $this->allowedStatuses) . '.';
        }

        return empty($errors) ? true : $errors;
    }

    private function validPositiveId(mixed $value): bool
    {
        return is_numeric($value) && (int) $value > 0;
    }

    private function memberListError(array $errors): array
    {
        return [
            'success' => false,
            'message' => 'Validasi GraphQL Gateway gagal.',
            'members' => [],
            'errors' => $errors,
        ];
    }

    private function memberSingleError(array $errors): array
    {
        return [
            'success' => false,
            'message' => 'Validasi GraphQL Gateway gagal.',
            'member' => null,
            'errors' => $errors,
        ];
    }

    private function memberMutationError(array $errors): array
    {
        return [
            'success' => false,
            'message' => 'Validasi GraphQL Gateway gagal.',
            'member' => null,
            'data' => null,
            'errors' => $errors,
        ];
    }

    private function memberListPayload(array $result): array
    {
        return [
            'success' => (bool) ($result['success'] ?? false),
            'message' => $result['message'] ?? 'Request member selesai.',
            'members' => is_array($result['data'] ?? null) ? $result['data'] : [],
            'errors' => $result['errors'] ?? null,
        ];
    }

    private function memberSinglePayload(array $result): array
    {
        return [
            'success' => (bool) ($result['success'] ?? false),
            'message' => $result['message'] ?? 'Request member selesai.',
            'member' => is_array($result['data'] ?? null) ? $result['data'] : null,
            'errors' => $result['errors'] ?? null,
        ];
    }

    private function memberMutationPayload(array $result): array
    {
        return [
            'success' => (bool) ($result['success'] ?? false),
            'message' => $result['message'] ?? 'Request member selesai.',
            'member' => is_array($result['data'] ?? null) ? $result['data'] : null,
            'data' => $result['data'] ?? null,
            'errors' => $result['errors'] ?? null,
        ];
    }
}

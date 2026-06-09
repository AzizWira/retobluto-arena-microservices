<?php

namespace App\GraphQL\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class AuthServiceClient
{
    private function baseUrl(): string
    {
        return rtrim(env('AUTH_SERVICE_URL', 'http://auth-service:8000'), '/');
    }

    private function client(?string $authorization = null)
    {
        $client = Http::acceptJson()
            ->timeout((int) env('GATEWAY_TIMEOUT', 8));

        if ($authorization) {
            $client = $client->withHeaders([
                'Authorization' => $authorization,
            ]);
        }

        return $client;
    }

    public function me(?string $authorization): array
    {
        if (!$authorization) {
            return $this->localError('Token wajib diisi.', [
                'authorization' => ['Token wajib diisi.'],
            ], 401);
        }

        $response = $this->client($authorization)
            ->get($this->baseUrl() . '/api/me');

        return $this->payload($response, 'Gagal mengambil data user.');
    }

    public function validateToken(?string $authorization): array
    {
        if (!$authorization) {
            return [
                'success' => false,
                'valid' => false,
                'message' => 'Token wajib diisi.',
                'data' => null,
                'errors' => [
                    'authorization' => ['Token wajib diisi.'],
                ],
                'status' => 401,
            ];
        }

        $response = $this->client($authorization)
            ->post($this->baseUrl() . '/api/validate-token');

        $json = $response->json();

        if (!is_array($json)) {
            return $this->localError('Response Auth Service tidak valid.', [
                'response' => ['Response Auth Service tidak valid.'],
            ], $response->status());
        }

        return [
            'success' => $response->successful() && (bool) ($json['success'] ?? false),
            'valid' => (bool) ($json['valid'] ?? false),
            'message' => $json['message'] ?? 'Validasi token selesai.',
            'data' => [
                'user' => $json['user'] ?? null,
            ],
            'errors' => $json['errors'] ?? $json['error'] ?? null,
            'status' => $response->status(),
        ];
    }

    public function adminCreateMember(array $payload, ?string $authorization): array
    {
        if (!$authorization) {
            return $this->localError('Token admin wajib diisi.', [
                'authorization' => ['Token admin wajib diisi.'],
            ], 401);
        }

        $response = $this->client($authorization)
            ->post($this->baseUrl() . '/api/admin/members', $payload);

        return $this->payload($response, 'Gagal membuat member dari Auth Service.');
    }

    public function deleteMemberAuthAccount(array $payload, ?string $authorization): array
    {
        if (!$authorization) {
            return $this->localError('Token admin wajib diisi.', [
                'authorization' => ['Token admin wajib diisi.'],
            ], 401);
        }

        $response = $this->client($authorization)
            ->delete($this->baseUrl() . '/api/admin/members/auth-account', $payload);

        return $this->payload($response, 'Gagal menghapus akun Auth Service member.');
    }

    private function payload(Response $response, string $fallbackMessage): array
    {
        $json = $response->json();

        if (!is_array($json)) {
            return $this->localError($fallbackMessage, [
                'response' => ['Response Auth Service tidak valid.'],
            ], $response->status());
        }

        return [
            'success' => $response->successful() && (bool) ($json['success'] ?? true),
            'valid' => $json['valid'] ?? null,
            'message' => $json['message'] ?? $fallbackMessage,
            'data' => $json['data'] ?? null,
            'errors' => $json['errors'] ?? $json['error'] ?? $json['member_sync'] ?? null,
            'status' => $response->status(),
        ];
    }

    private function localError(string $message, array $errors, int $status): array
    {
        return [
            'success' => false,
            'valid' => false,
            'message' => $message,
            'data' => null,
            'errors' => $errors,
            'status' => $status,
        ];
    }
}

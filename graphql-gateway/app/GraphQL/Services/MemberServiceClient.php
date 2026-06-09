<?php

namespace App\GraphQL\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class MemberServiceClient
{
    private function baseUrl(): string
    {
        return rtrim(env('MEMBER_SERVICE_URL', 'http://member-service:8000'), '/');
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

    public function all(array $filters, ?string $authorization): array
    {
        $response = $this->client($authorization)
            ->get($this->baseUrl() . '/api/members', $this->cleanQuery($filters));

        return $this->payload($response, 'Gagal mengambil data member.');
    }

    public function find(int|string $id, ?string $authorization): array
    {
        $response = $this->client($authorization)
            ->get($this->baseUrl() . "/api/members/{$id}");

        return $this->payload($response, 'Gagal mengambil detail member.');
    }

    public function byUserId(int|string $userId, ?string $authorization): array
    {
        $response = $this->client($authorization)
            ->get($this->baseUrl() . "/api/members/user/{$userId}");

        return $this->payload($response, 'Gagal mengambil member berdasarkan user id.');
    }

    public function profile(?string $authorization): array
    {
        $response = $this->client($authorization)
            ->get($this->baseUrl() . '/api/profile');

        return $this->payload($response, 'Gagal mengambil profil member.');
    }

    public function updateProfile(array $payload, ?string $authorization): array
    {
        $response = $this->client($authorization)
            ->put($this->baseUrl() . '/api/profile', $payload);

        return $this->payload($response, 'Gagal memperbarui profil member.');
    }

    public function update(int|string $id, array $payload, ?string $authorization): array
    {
        $response = $this->client($authorization)
            ->put($this->baseUrl() . "/api/members/{$id}", $payload);

        return $this->payload($response, 'Gagal memperbarui member.');
    }

    public function updateStatus(int|string $id, string $status, ?string $authorization): array
    {
        $response = $this->client($authorization)
            ->patch($this->baseUrl() . "/api/members/{$id}/status", [
                'status' => $status,
            ]);

        return $this->payload($response, 'Gagal memperbarui status member.');
    }

    public function delete(int|string $id, ?string $authorization): array
    {
        $response = $this->client($authorization)
            ->delete($this->baseUrl() . "/api/members/{$id}");

        return $this->payload($response, 'Gagal menghapus member.');
    }

    private function cleanQuery(array $query): array
    {
        return array_filter($query, fn($value) => $value !== null && $value !== '');
    }

    private function payload(Response $response, string $fallbackMessage): array
    {
        $json = $response->json();

        if (!is_array($json)) {
            return [
                'success' => false,
                'message' => $fallbackMessage,
                'data' => null,
                'errors' => [
                    'response' => ['Response Member Service tidak valid.'],
                ],
                'status' => $response->status(),
            ];
        }

        return [
            'success' => $response->successful() && (bool) ($json['success'] ?? true),
            'message' => $json['message'] ?? $fallbackMessage,
            'data' => $json['data'] ?? null,
            'errors' => $json['errors'] ?? $json['error'] ?? null,
            'status' => $response->status(),
        ];
    }
}

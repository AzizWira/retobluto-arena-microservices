<?php

namespace App\GraphQL\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class FieldServiceClient
{
    private function baseUrl(): string
    {
        return rtrim(env('FIELD_SERVICE_URL', 'http://field-service:8000'), '/');
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

    public function all(array $filters = []): array
    {
        $response = $this->client()
            ->get($this->baseUrl() . '/api/fields', array_filter($filters, fn($value) => $value !== null && $value !== ''));

        return $this->unwrapData($response);
    }

    public function available(): array
    {
        $response = $this->client()
            ->get($this->baseUrl() . '/api/fields/available');

        return $this->unwrapData($response);
    }

    public function find(int|string $id): ?array
    {
        $response = $this->client()
            ->get($this->baseUrl() . "/api/fields/{$id}");

        return $this->unwrapNullableData($response);
    }

    public function schedule(int|string $id, ?string $date = null): array
    {
        $response = $this->client()
            ->get($this->baseUrl() . "/api/fields/{$id}/booking-schedule", array_filter([
                'date' => $date,
            ]));

        return $response->json() ?? [
            'success' => false,
            'message' => 'Response Field Service tidak valid.',
        ];
    }

    public function create(array $payload, ?string $authorization): array
    {
        $response = $this->client($authorization)
            ->post($this->baseUrl() . '/api/fields', $payload);

        return $this->unwrapMutation($response);
    }

    public function update(int|string $id, array $payload, ?string $authorization): array
    {
        $response = $this->client($authorization)
            ->put($this->baseUrl() . "/api/fields/{$id}", $payload);

        return $this->unwrapMutation($response);
    }

    public function updateStatus(int|string $id, string $status, ?string $authorization): array
    {
        $response = $this->client($authorization)
            ->patch($this->baseUrl() . "/api/fields/{$id}/status", [
                'status' => $status,
            ]);

        return $this->unwrapMutation($response);
    }

    public function delete(int|string $id, ?string $authorization): array
    {
        $response = $this->client($authorization)
            ->delete($this->baseUrl() . "/api/fields/{$id}");

        $json = $response->json() ?? [];

        return [
            'success' => $response->successful() && ($json['success'] ?? true),
            'message' => $json['message'] ?? ($response->successful() ? 'Lapangan berhasil dihapus.' : 'Gagal menghapus lapangan.'),
            'data' => $json['data'] ?? null,
            'errors' => $json['errors'] ?? null,
        ];
    }

    private function unwrapData(Response $response): array
    {
        if (!$response->successful()) {
            return [];
        }

        $json = $response->json();

        return is_array($json) ? ($json['data'] ?? []) : [];
    }

    private function unwrapNullableData(Response $response): ?array
    {
        if (!$response->successful()) {
            return null;
        }

        $json = $response->json();

        return is_array($json) ? ($json['data'] ?? null) : null;
    }

    private function unwrapMutation(Response $response): array
    {
        $json = $response->json() ?? [];

        if (!$response->successful()) {
            return [
                'success' => false,
                'message' => $json['message'] ?? 'Request ke Field Service gagal.',
                'data' => null,
                'errors' => $json['errors'] ?? $json['error'] ?? null,
            ];
        }

        return [
            'success' => true,
            'message' => $json['message'] ?? 'Request berhasil.',
            'data' => $json['data'] ?? null,
            'errors' => null,
        ];
    }
}

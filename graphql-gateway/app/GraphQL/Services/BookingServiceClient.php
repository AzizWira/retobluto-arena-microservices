<?php

namespace App\GraphQL\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class BookingServiceClient
{
    private function baseUrl(): string
    {
        return rtrim(env('BOOKING_SERVICE_URL', 'http://booking-service:8000'), '/');
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
            ->get($this->baseUrl() . '/api/bookings', $this->cleanQuery($filters));

        return $this->payload($response, 'Gagal mengambil data booking.');
    }

    public function find(int|string $id, ?string $authorization): array
    {
        $response = $this->client($authorization)
            ->get($this->baseUrl() . "/api/bookings/{$id}");

        return $this->payload($response, 'Gagal mengambil detail booking.');
    }

    public function memberBookings(?string $authorization): array
    {
        $response = $this->client($authorization)
            ->get($this->baseUrl() . '/api/member/bookings');

        return $this->payload($response, 'Gagal mengambil booking aktif member.');
    }

    public function memberHistory(?string $authorization): array
    {
        $response = $this->client($authorization)
            ->get($this->baseUrl() . '/api/member/bookings/history');

        return $this->payload($response, 'Gagal mengambil riwayat booking member.');
    }

    public function adminRequests(?string $authorization): array
    {
        $response = $this->client($authorization)
            ->get($this->baseUrl() . '/api/admin/booking-requests');

        return $this->payload($response, 'Gagal mengambil request booking admin.');
    }

    public function fieldSchedule(int|string $fieldId, array $filters = []): array
    {
        $response = $this->client()
            ->get($this->baseUrl() . "/api/bookings/field/{$fieldId}/schedule", $this->cleanQuery($filters));

        return $this->payload($response, 'Gagal mengambil jadwal booking lapangan.');
    }

    public function byMember(int|string $memberId, ?string $authorization): array
    {
        $response = $this->client($authorization)
            ->get($this->baseUrl() . "/api/bookings/member/{$memberId}");

        return $this->payload($response, 'Gagal mengambil booking berdasarkan member.');
    }

    public function create(array $payload, ?string $authorization): array
    {
        $response = $this->client($authorization)
            ->post($this->baseUrl() . '/api/bookings', $payload);

        return $this->payload($response, 'Gagal membuat booking.');
    }

    public function approve(int|string $id, ?string $authorization): array
    {
        $response = $this->client($authorization)
            ->post($this->baseUrl() . "/api/admin/bookings/{$id}/approve", []);

        return $this->payload($response, 'Gagal approve booking.');
    }

    public function reject(int|string $id, ?string $authorization, ?string $reason = null): array
    {
        $response = $this->client($authorization)
            ->post($this->baseUrl() . "/api/admin/bookings/{$id}/reject", [
                'rejection_reason' => $reason,
            ]);

        return $this->payload($response, 'Gagal reject booking.');
    }

    public function cancel(int|string $id, ?string $authorization): array
    {
        $response = $this->client($authorization)
            ->post($this->baseUrl() . "/api/member/bookings/{$id}/cancel", []);

        return $this->payload($response, 'Gagal membatalkan booking.');
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
                    'response' => ['Response Booking Service tidak valid.'],
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

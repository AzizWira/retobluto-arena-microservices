<?php

namespace App\GraphQL\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class NotificationServiceClient
{
    private function baseUrl(): string
    {
        return rtrim(env('NOTIFICATION_SERVICE_URL', 'http://notification-service:8000'), '/');
    }

    private function client(?string $authorization = null)
    {
        $client = Http::acceptJson()
            ->connectTimeout(2)
            ->timeout((int) env('GATEWAY_TIMEOUT', 8));

        if ($authorization) {
            $client = $client->withHeaders([
                'Authorization' => $authorization,
            ]);
        }

        return $client;
    }

    public function logs(array $filters, ?string $authorization): array
    {
        try {
            $response = $this->client($authorization)
                ->get($this->baseUrl() . '/api/notifications/logs', $this->cleanQuery($filters));

            return $this->payload($response, 'Gagal mengambil log notifikasi.');
        } catch (ConnectionException $e) {
            return $this->connectionError($e, 'Notification Service timeout saat mengambil log notifikasi.');
        } catch (\Exception $e) {
            return $this->generalError($e, 'Terjadi kesalahan saat mengambil log notifikasi.');
        }
    }

    public function log(int|string $id, ?string $authorization): array
    {
        try {
            $response = $this->client($authorization)
                ->get($this->baseUrl() . "/api/notifications/logs/{$id}");

            return $this->payload($response, 'Gagal mengambil detail log notifikasi.');
        } catch (ConnectionException $e) {
            return $this->connectionError($e, 'Notification Service timeout saat mengambil detail log notifikasi.');
        } catch (\Exception $e) {
            return $this->generalError($e, 'Terjadi kesalahan saat mengambil detail log notifikasi.');
        }
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
                    'response' => ['Response Notification Service tidak valid.'],
                    'status' => [$response->status()],
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

    private function connectionError(ConnectionException $e, string $message): array
    {
        return [
            'success' => false,
            'message' => $message,
            'data' => null,
            'errors' => [
                'connection' => [$e->getMessage()],
            ],
            'status' => 503,
        ];
    }

    private function generalError(\Exception $e, string $message): array
    {
        return [
            'success' => false,
            'message' => $message,
            'data' => null,
            'errors' => [
                'exception' => [$e->getMessage()],
            ],
            'status' => 500,
        ];
    }
}

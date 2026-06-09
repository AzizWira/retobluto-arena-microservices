<?php

namespace App\GraphQL\Resolvers;

use Illuminate\Support\Facades\Http;

class HealthResolver
{
    public function resolve(): array
    {
        return [
            'auth' => $this->checkService(env('AUTH_SERVICE_URL'), 'Auth Service'),
            'member' => $this->checkService(env('MEMBER_SERVICE_URL'), 'Member Service'),
            'field' => $this->checkService(env('FIELD_SERVICE_URL'), 'Field Service'),
            'booking' => $this->checkService(env('BOOKING_SERVICE_URL'), 'Booking Service'),
            'notification' => $this->checkService(env('NOTIFICATION_SERVICE_URL'), 'Notification Service'),
        ];
    }

    private function checkService(?string $baseUrl, string $name): array
    {
        if (!$baseUrl) {
            return [
                'ok' => false,
                'status' => 0,
                'message' => "{$name} URL belum dikonfigurasi.",
            ];
        }

        try {
            $response = Http::acceptJson()
                ->connectTimeout(2)
                ->timeout((int) env('GATEWAY_TIMEOUT', 8))
                ->get(rtrim($baseUrl, '/') . '/api/health');

            return [
                'ok' => $response->successful(),
                'status' => $response->status(),
                'message' => $response->json('message') ?? $response->json('service') ?? "{$name} checked.",
            ];
        } catch (\Exception $e) {
            return [
                'ok' => false,
                'status' => 0,
                'message' => $e->getMessage(),
            ];
        }
    }
}

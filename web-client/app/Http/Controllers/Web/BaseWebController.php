<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Client\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Http;

class BaseWebController extends Controller
{
    protected function token(): ?string
    {
        return session('access_token');
    }

    protected function user(): ?array
    {
        return session('user');
    }

    protected function role(): ?string
    {
        return session('user.role');
    }

    protected function isLoggedIn(): bool
    {
        return session()->has('access_token');
    }

    protected function isAdmin(): bool
    {
        return $this->role() === 'admin';
    }

    protected function isMember(): bool
    {
        return $this->role() === 'member';
    }

    protected function authClient(int $timeout = 8)
    {
        return Http::acceptJson()
            ->connectTimeout(2)
            ->timeout($timeout)
            ->baseUrl(rtrim(env('AUTH_SERVICE_URL'), '/'));
    }

    protected function memberClient(int $timeout = 8)
    {
        return Http::acceptJson()
            ->connectTimeout(2)
            ->timeout($timeout)
            ->withToken($this->token())
            ->baseUrl(rtrim(env('MEMBER_SERVICE_URL'), '/'));
    }

    protected function fieldClient(int $timeout = 8)
    {
        $client = Http::acceptJson()
            ->connectTimeout(2)
            ->timeout($timeout)
            ->baseUrl(rtrim(env('FIELD_SERVICE_URL'), '/'));

        if ($this->token()) {
            $client = $client->withToken($this->token());
        }

        return $client;
    }

    protected function bookingClient(int $timeout = 10)
    {
        return Http::acceptJson()
            ->connectTimeout(2)
            ->timeout($timeout)
            ->withToken($this->token())
            ->baseUrl(rtrim(env('BOOKING_SERVICE_URL'), '/'));
    }

    protected function notificationClient(int $timeout = 6)
    {
        return Http::acceptJson()
            ->connectTimeout(2)
            ->timeout($timeout)
            ->withToken($this->token())
            ->baseUrl(rtrim(env('NOTIFICATION_SERVICE_URL'), '/'));
    }

    protected function clearAuthSessionOnly(): void
    {
        session()->forget([
            'access_token',
            'token_type',
            'user',
        ]);
    }

    protected function clearExpiredSession(): void
    {
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }

    protected function expiredSessionRedirect(?string $message = null): RedirectResponse
    {
        $role = $this->role();

        $this->clearExpiredSession();

        if ($role === 'admin') {
            return redirect()
                ->route('login.admin')
                ->with('error', $message ?? 'Sesi admin telah berakhir. Silakan login kembali.');
        }

        return redirect()
            ->route('login.member')
            ->with('error', $message ?? 'Sesi member telah berakhir. Silakan login kembali.');
    }

    protected function requireAdmin()
    {
        if (!$this->isLoggedIn()) {
            return redirect()
                ->route('login.admin')
                ->with('error', 'Silakan login sebagai admin.');
        }

        if (!$this->tokenIsValid()) {
            return $this->expiredSessionRedirect('Sesi admin telah berakhir. Silakan login kembali.');
        }

        if (!$this->isAdmin()) {
            return redirect()
                ->route('login.member')
                ->with('error', 'Akses admin ditolak.');
        }

        return null;
    }

    protected function requireMember()
    {
        if (!$this->isLoggedIn()) {
            return redirect()
                ->route('login.member')
                ->with('error', 'Silakan login sebagai member.');
        }

        if (!$this->tokenIsValid()) {
            return $this->expiredSessionRedirect('Sesi member telah berakhir. Silakan login kembali.');
        }

        if (!$this->isMember()) {
            return redirect()
                ->route('login.admin')
                ->with('error', 'Akses member ditolak.');
        }

        return null;
    }

    protected function tokenIsValid(): bool
    {
        if (!$this->token()) {
            return false;
        }

        try {
            $response = $this->authClient(5)
                ->withToken($this->token())
                ->post('/api/validate-token');

            if ($response->successful() && $response->json('success') !== false) {
                return true;
            }

            if ($response->status() === 405) {
                $response = $this->authClient(5)
                    ->withToken($this->token())
                    ->get('/api/validate-token');

                return $response->successful() && $response->json('success') !== false;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    protected function apiUnauthorized(?Response $response): bool
    {
        if (!$response) {
            return false;
        }

        $message = strtolower((string) ($response->json('message') ?? ''));

        if ($response->status() === 419) {
            return true;
        }

        if ($response->status() === 401) {
            return str_contains($message, 'token')
                || str_contains($message, 'expired')
                || str_contains($message, 'unauthenticated')
                || str_contains($message, 'unauthorized')
                || str_contains($message, 'jwt')
                || str_contains($message, 'logout')
                || str_contains($message, 'blacklisted');
        }

        return str_contains($message, 'token expired')
            || str_contains($message, 'token tidak valid')
            || str_contains($message, 'token sudah expired')
            || str_contains($message, 'token sudah logout')
            || str_contains($message, 'token tidak ditemukan')
            || str_contains($message, 'jwt');
    }

    protected function apiError(Response $response, string $fallback = 'Terjadi kesalahan.'): string
    {
        if ($this->apiUnauthorized($response)) {
            $this->clearExpiredSession();

            return 'Sesi telah berakhir. Silakan login kembali.';
        }

        $json = $response->json();

        if (isset($json['message']) && is_string($json['message'])) {
            return $json['message'];
        }

        if (isset($json['error']) && is_string($json['error'])) {
            return $json['error'];
        }

        if (isset($json['errors']) && is_array($json['errors'])) {
            $firstError = collect($json['errors'])->flatten()->first();

            if ($firstError) {
                return (string) $firstError;
            }
        }

        return $fallback;
    }

    protected function dataList(Response $response): array
    {
        $data = $response->json('data');

        if (!is_array($data)) {
            return [];
        }

        if (isset($data['data']) && is_array($data['data'])) {
            return $data['data'];
        }

        return $data;
    }

    protected function dataItem(Response $response): ?array
    {
        $data = $response->json('data');

        return is_array($data) ? $data : null;
    }

    protected function countData(array $items): int
    {
        return count($items);
    }

    protected function onlyFilled(array $data): array
    {
        return array_filter($data, function ($value) {
            return $value !== null && $value !== '';
        });
    }
}

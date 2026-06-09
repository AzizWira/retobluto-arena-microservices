<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;

class AuthPageController extends BaseWebController
{
    public function memberLoginPage()
    {
        if ($this->isLoggedIn()) {
            if ($this->tokenIsValid()) {
                return $this->isAdmin()
                    ? redirect()->route('admin.dashboard')
                    : redirect()->route('member.home');
            }

            $this->clearAuthSessionOnly();

            return redirect()
                ->route('login.member')
                ->with('error', 'Sesi sebelumnya telah berakhir. Silakan login kembali.');
        }

        return view('auth.member-login');
    }

    public function adminLoginPage()
    {
        if ($this->isLoggedIn()) {
            if ($this->tokenIsValid()) {
                return $this->isAdmin()
                    ? redirect()->route('admin.dashboard')
                    : redirect()->route('member.home');
            }

            $this->clearAuthSessionOnly();

            return redirect()
                ->route('login.admin')
                ->with('error', 'Sesi sebelumnya telah berakhir. Silakan login kembali.');
        }

        return view('auth.admin-login');
    }

    public function registerPage()
    {
        if ($this->isLoggedIn()) {
            if ($this->tokenIsValid()) {
                return $this->isAdmin()
                    ? redirect()->route('admin.dashboard')
                    : redirect()->route('member.home');
            }

            $this->clearAuthSessionOnly();
        }

        return view('auth.register');
    }

    public function verifyOtpPage()
    {
        if ($this->isLoggedIn()) {
            if ($this->tokenIsValid()) {
                return $this->isAdmin()
                    ? redirect()->route('admin.dashboard')
                    : redirect()->route('member.home');
            }

            $this->clearAuthSessionOnly();
        }

        return view('auth.verify-otp');
    }

    public function memberLogin(Request $request)
    {
        return $this->loginByRole($request, 'member');
    }

    public function adminLogin(Request $request)
    {
        return $this->loginByRole($request, 'admin');
    }

    private function loginByRole(Request $request, string $role)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        /*
         * Penting:
         * Bersihkan token lama sebelum login baru.
         * Ini mencegah cookie/session lama membuat proses login gagal setelah migrate:fresh
         * atau setelah JWT lama expired.
         */
        $this->clearAuthSessionOnly();

        $endpoint = $role === 'admin'
            ? '/api/admin/login'
            : '/api/member/login';

        try {
            $response = $this->authClient()->post($endpoint, [
                'email' => $validated['email'],
                'password' => $validated['password'],
            ]);
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Auth Service tidak dapat dihubungi.');
        }

        $data = $response->json();
        $data = is_array($data) ? $data : [];

        if (!$response->successful()) {
            if (
                $role === 'member'
                && ($data['requires_otp'] ?? false) === true
            ) {
                session([
                    'register_email' => $data['data']['email'] ?? $validated['email'],
                    'register_name' => $data['data']['name'] ?? null,
                    'otp_debug' => $data['data']['otp_debug'] ?? null,
                ]);

                return redirect()
                    ->route('register.verifyOtpPage')
                    ->with('success', $data['message'] ?? 'Akun perlu verifikasi OTP.');
            }

            return back()
                ->withInput()
                ->with('error', $this->responseMessage($response, 'Login gagal.'));
        }

        if (!isset($data['access_token'], $data['user'])) {
            return back()
                ->withInput()
                ->with('error', 'Response login dari Auth Service tidak valid.');
        }

        if (($data['user']['role'] ?? null) !== $role) {
            return back()
                ->withInput()
                ->with('error', 'Role akun tidak sesuai dengan halaman login.');
        }

        $request->session()->regenerate();

        session([
            'access_token' => $data['access_token'],
            'token_type' => $data['token_type'] ?? 'Bearer',
            'user' => $data['user'],
        ]);

        session()->forget([
            'register_name',
            'register_email',
            'otp_debug',
        ]);

        return $role === 'admin'
            ? redirect()->route('admin.dashboard')->with('success', 'Login admin berhasil.')
            : redirect()->route('member.home')->with('success', 'Login member berhasil.');
    }

    public function requestOtp(Request $request)
    {
        if ($this->isLoggedIn() && !$this->tokenIsValid()) {
            $this->clearAuthSessionOnly();
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        try {
            $response = $this->authClient()->post('/api/member/register/request-otp', [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
            ]);
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Auth Service tidak dapat dihubungi.');
        }

        if (!$response->successful()) {
            return back()
                ->withInput()
                ->with('error', $this->responseMessage($response, 'Gagal membuat OTP.'));
        }

        $data = $response->json();
        $data = is_array($data) ? $data : [];

        session([
            'register_name' => $validated['name'],
            'register_email' => $validated['email'],
            'otp_debug' => $data['data']['otp_debug'] ?? $data['otp_debug'] ?? null,
        ]);

        return redirect()
            ->route('register.verifyOtpPage')
            ->with('success', 'OTP berhasil dikirim ke email. Silakan masukkan kode OTP.');
    }

    public function verifyOtp(Request $request)
    {
        if ($this->isLoggedIn() && !$this->tokenIsValid()) {
            $this->clearAuthSessionOnly();
        }

        $validated = $request->validate([
            'email' => ['required', 'email'],
            'otp' => ['required', 'string', 'size:6'],
        ]);

        try {
            $response = $this->authClient()->post('/api/member/register/verify', [
                'email' => $validated['email'],
                'otp' => $validated['otp'],
            ]);
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Auth Service tidak dapat dihubungi.');
        }

        if (!$response->successful()) {
            return back()
                ->withInput()
                ->with('error', $this->responseMessage($response, 'Verifikasi OTP gagal.'));
        }

        $data = $response->json();
        $data = is_array($data) ? $data : [];

        if (!isset($data['access_token'], $data['user'])) {
            session()->forget([
                'register_name',
                'register_email',
                'otp_debug',
            ]);

            return redirect()
                ->route('login.member')
                ->with('success', 'Registrasi berhasil. Silakan login sebagai member.');
        }

        $request->session()->regenerate();

        session([
            'access_token' => $data['access_token'],
            'token_type' => $data['token_type'] ?? 'Bearer',
            'user' => $data['user'],
        ]);

        session()->forget([
            'register_name',
            'register_email',
            'otp_debug',
        ]);

        return redirect()
            ->route('member.home')
            ->with('success', 'Registrasi berhasil. Anda sudah login sebagai member.');
    }

    public function resendOtp(Request $request)
    {
        if ($this->isLoggedIn() && !$this->tokenIsValid()) {
            $this->clearAuthSessionOnly();
        }

        $email = $request->input('email') ?: session('register_email');

        if (!$email) {
            return redirect()
                ->route('register.member')
                ->with('error', 'Email tidak ditemukan. Silakan lakukan registrasi ulang.');
        }

        try {
            $response = $this->authClient()->post('/api/member/register/resend-otp', [
                'email' => $email,
            ]);
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Auth Service tidak dapat dihubungi.');
        }

        if (!$response->successful()) {
            return back()
                ->withInput()
                ->with('error', $this->responseMessage($response, 'Gagal mengirim ulang OTP.'));
        }

        $data = $response->json();
        $data = is_array($data) ? $data : [];

        session([
            'register_email' => $data['data']['email'] ?? $email,
            'otp_debug' => $data['data']['otp_debug'] ?? null,
        ]);

        return back()->with('success', $data['message'] ?? 'Kode OTP baru berhasil dikirim.');
    }

    public function logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login.member')
            ->with('success', 'Logout berhasil.');
    }

    private function responseMessage(Response $response, string $fallback): string
    {
        $data = $response->json();
        $data = is_array($data) ? $data : [];

        $message = $data['message'] ?? $fallback;

        if (!empty($data['errors']) && is_array($data['errors'])) {
            $errors = collect($data['errors'])
                ->flatten()
                ->filter()
                ->implode(' ');

            if ($errors !== '') {
                $message .= ' ' . $errors;
            }
        }

        if (!empty($data['error'])) {
            $message .= ' ' . $data['error'];
        }

        return $message;
    }
}

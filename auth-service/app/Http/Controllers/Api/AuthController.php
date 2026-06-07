<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;

class AuthController extends Controller
{
    public function adminLogin(Request $request)
    {
        return $this->loginByRole($request, 'admin');
    }

    public function memberLogin(Request $request)
    {
        return $this->loginByRole($request, 'member');
    }

    private function loginByRole(Request $request, string $role)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            $user = User::where('email', $validated['email'])
                ->where('role', $role)
                ->first();

            if (!$user || !Hash::check($validated['password'], $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email atau password salah',
                ], 401);
            }

            if ($role === 'member' && !$user->is_verified) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun member belum diverifikasi',
                ], 403);
            }

            $token = JWTAuth::fromUser($user);

            return $this->respondWithToken($token, $user, 'Login berhasil');
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat login',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function requestMemberOtp(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:100',
                'email' => 'required|email|max:150|unique:users,email',
                'password' => 'required|string|min:6',
            ]);

            $otp = (string) random_int(100000, 999999);

            OtpCode::where('email', $validated['email'])
                ->whereNull('verified_at')
                ->delete();

            OtpCode::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password_hash' => Hash::make($validated['password']),
                'otp_hash' => Hash::make($otp),
                'expires_at' => now()->addMinutes(10),
            ]);

            try {
                Redis::publish('otp_requested', json_encode([
                    'type' => 'otp_requested',
                    'email' => $validated['email'],
                    'name' => $validated['name'],
                    'otp' => $otp,
                    'expired_at' => now()->addMinutes(10)->toDateTimeString(),
                ]));
            } catch (\Exception $e) {
                // Untuk tahap Push 3, Redis event tidak menggagalkan request OTP.
                // Notification Service baru akan dikerjakan pada push berikutnya.
            }

            return response()->json([
                'success' => true,
                'message' => 'Kode OTP berhasil dibuat',
                'data' => [
                    'email' => $validated['email'],
                    'expired_in_minutes' => 10,
                    'otp_debug' => config('app.debug') ? $otp : null,
                ],
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat membuat OTP',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function verifyMemberOtp(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email',
                'otp' => 'required|string|size:6',
            ]);

            $otpRecord = OtpCode::where('email', $validated['email'])
                ->whereNull('verified_at')
                ->latest()
                ->first();

            if (!$otpRecord) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode OTP tidak ditemukan',
                ], 404);
            }

            if (now()->greaterThan($otpRecord->expires_at)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode OTP sudah expired',
                ], 422);
            }

            if (!Hash::check($validated['otp'], $otpRecord->otp_hash)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode OTP tidak sesuai',
                ], 422);
            }

            $user = User::create([
                'name' => $otpRecord->name,
                'email' => $otpRecord->email,
                'password' => $otpRecord->password_hash,
                'role' => 'member',
                'is_verified' => true,
                'email_verified_at' => now(),
            ]);

            $otpRecord->update([
                'verified_at' => now(),
            ]);

            try {
                Redis::publish('member_registered', json_encode([
                    'type' => 'member_registered',
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ]));
            } catch (\Exception $e) {
                // Event ini akan dipakai Member Service nanti.
            }

            $token = JWTAuth::fromUser($user);

            return $this->respondWithToken($token, $user, 'Registrasi member berhasil');
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat verifikasi OTP',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function me()
    {
        try {
            $user = auth('api')->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token tidak valid',
                ], 401);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data user berhasil diambil',
                'data' => $this->userPayload($user),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak valid atau expired',
            ], 401);
        }
    }

    public function validateToken(Request $request)
    {
        try {
            $token = $request->bearerToken();

            if (!$token) {
                return response()->json([
                    'success' => false,
                    'valid' => false,
                    'message' => 'Token tidak ditemukan',
                ], 401);
            }

            $user = JWTAuth::setToken($token)->authenticate();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'valid' => false,
                    'message' => 'Token tidak valid',
                ], 401);
            }

            return response()->json([
                'success' => true,
                'valid' => true,
                'message' => 'Token valid',
                'user' => $this->userPayload($user),
            ], 200);
        } catch (TokenExpiredException $e) {
            return $this->invalidTokenResponse('Token sudah expired');
        } catch (TokenBlacklistedException $e) {
            return $this->invalidTokenResponse('Token sudah logout');
        } catch (TokenInvalidException $e) {
            return $this->invalidTokenResponse('Token tidak valid');
        } catch (JWTException $e) {
            return $this->invalidTokenResponse('Token tidak dapat diproses');
        } catch (\Exception $e) {
            return $this->invalidTokenResponse('Token tidak valid atau expired');
        }
    }

    public function refresh()
    {
        try {
            $token = JWTAuth::getToken();

            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token tidak ditemukan',
                ], 401);
            }

            $user = JWTAuth::authenticate($token);
            $newToken = JWTAuth::refresh($token);

            return $this->respondWithToken($newToken, $user, 'Token berhasil diperbarui');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak dapat diperbarui',
            ], 401);
        }
    }

    public function logout()
    {
        try {
            $token = JWTAuth::getToken();

            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token tidak ditemukan',
                ], 401);
            }

            JWTAuth::invalidate($token);

            return response()->json([
                'success' => true,
                'message' => 'Logout berhasil',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak valid atau sudah logout',
            ], 401);
        }
    }

    private function respondWithToken(string $token, User $user, string $message)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'token_type' => 'Bearer',
            'access_token' => $token,
            'expires_in' => (int) config('jwt.ttl', 60) * 60,
            'user' => $this->userPayload($user),
        ], 200);
    }

    private function userPayload(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'is_verified' => (bool) $user->is_verified,
        ];
    }

    private function invalidTokenResponse(string $message)
    {
        return response()->json([
            'success' => false,
            'valid' => false,
            'message' => $message,
        ], 401);
    }
}

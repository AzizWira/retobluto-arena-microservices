<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

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

    public function adminCreateMember(Request $request)
    {
        try {
            $admin = auth('api')->user();

            if (!$admin || $admin->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses hanya untuk admin.',
                ], 403);
            }

            $validated = $request->validate([
                'name' => 'required|string|max:100',
                'email' => 'required|email|max:150|unique:users,email',
                'password' => 'required|string|min:6',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string',
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'member',
                'is_verified' => false,
                'email_verified_at' => null,
            ]);

            $memberSyncResult = $this->syncMemberProfile($user, [
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'status' => 'inactive',
            ]);

            if (!$memberSyncResult['success']) {
                $user->delete();

                return response()->json([
                    'success' => false,
                    'message' => 'Member gagal dibuat karena profil member tidak dapat disinkronkan',
                    'member_sync' => $memberSyncResult,
                ], 502);
            }

            $otpData = $this->createOtpForVerification($user->name, $user->email, $user->password);

            return response()->json([
                'success' => true,
                'message' => 'Member berhasil dibuat oleh admin. Akun perlu verifikasi OTP sebelum aktif.',
                'data' => [
                    'user' => $this->userPayload($user),
                    'member_sync' => $memberSyncResult,
                    'otp_debug' => config('app.debug') ? $otpData['otp'] : null,
                    'expired_in_minutes' => 10,
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
                'message' => 'Gagal membuat member dari admin',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function adminDeleteMemberAuthAccount(Request $request)
    {
        try {
            $admin = auth('api')->user();

            if (!$admin || $admin->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak. Hanya admin yang dapat menghapus akun member.',
                ], 403);
            }

            $validated = $request->validate([
                'user_id' => ['nullable', 'integer'],
                'email' => ['nullable', 'email'],
            ]);

            if (empty($validated['user_id']) && empty($validated['email'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'user_id atau email wajib dikirim.',
                ], 422);
            }

            $query = User::where('role', 'member');

            if (!empty($validated['user_id'])) {
                $query->where('id', $validated['user_id']);
            } else {
                $query->where('email', strtolower($validated['email']));
            }

            $user = $query->first();

            if (!$user) {
                return response()->json([
                    'success' => true,
                    'message' => 'Akun Auth Service tidak ditemukan atau sudah terhapus.',
                    'data' => [
                        'deleted' => false,
                    ],
                ]);
            }

            $email = $user->email;

            OtpCode::where('email', $email)->delete();

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Akun member di Auth Service berhasil dihapus.',
                'data' => [
                    'deleted' => true,
                    'email' => $email,
                ],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus akun member dari Auth Service.',
                'error' => $e->getMessage(),
            ], 500);
        }
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
                $otpData = $this->createOtpForVerification($user->name, $user->email, $user->password);

                return response()->json([
                    'success' => false,
                    'requires_otp' => true,
                    'message' => 'Akun member belum diverifikasi. Kode OTP telah dibuat.',
                    'data' => [
                        'email' => $user->email,
                        'name' => $user->name,
                        'expired_in_minutes' => 10,
                        'otp_debug' => config('app.debug') ? $otpData['otp'] : null,
                    ],
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
                // Redis event tidak menggagalkan request OTP.
                // Notification Service akan menangani event ini pada tahap berikutnya.
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

            $existingUser = User::where('email', $otpRecord->email)->first();

            if ($existingUser) {
                if ($existingUser->role !== 'member') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Email ini bukan akun member.',
                    ], 409);
                }

                if ($existingUser->is_verified) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Akun member sudah diverifikasi.',
                    ], 409);
                }

                $existingUser->update([
                    'is_verified' => true,
                    'email_verified_at' => now(),
                ]);

                $user = $existingUser->fresh();

                $memberSyncResult = $this->syncMemberProfile($user, [
                    'status' => 'active',
                ]);

                if (!$memberSyncResult['success']) {
                    $user->update([
                        'is_verified' => false,
                        'email_verified_at' => null,
                    ]);

                    return response()->json([
                        'success' => false,
                        'message' => 'Verifikasi gagal karena status profil member tidak dapat diaktifkan',
                        'member_sync' => $memberSyncResult,
                    ], 502);
                }
            } else {
                $user = User::create([
                    'name' => $otpRecord->name,
                    'email' => $otpRecord->email,
                    'password' => $otpRecord->password_hash,
                    'role' => 'member',
                    'is_verified' => true,
                    'email_verified_at' => now(),
                ]);

                $memberSyncResult = $this->syncMemberProfile($user, [
                    'status' => 'active',
                ]);

                if (!$memberSyncResult['success']) {
                    $user->delete();

                    return response()->json([
                        'success' => false,
                        'message' => 'Registrasi gagal karena profil member tidak dapat dibuat',
                        'member_sync' => $memberSyncResult,
                    ], 502);
                }
            }

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
                //
            }

            $token = JWTAuth::fromUser($user);

            return response()->json([
                'success' => true,
                'message' => 'Verifikasi member berhasil',
                'token_type' => 'Bearer',
                'access_token' => $token,
                'expires_in' => (int) config('jwt.ttl', 60) * 60,
                'user' => $this->userPayload($user),
                'member_sync' => $memberSyncResult,
            ], 200);
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

    public function resendMemberOtp(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => ['required', 'email'],
            ]);

            $email = strtolower($validated['email']);

            $existingUser = User::where('email', $email)
                ->where('role', 'member')
                ->first();

            if ($existingUser && $existingUser->is_verified) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun member sudah terverifikasi. Silakan login.',
                ], 422);
            }

            if ($existingUser) {
                $name = $existingUser->name;
                $passwordHash = $existingUser->password;
            } else {
                $previousOtp = OtpCode::where('email', $email)
                    ->whereNull('verified_at')
                    ->latest()
                    ->first();

                if (!$previousOtp) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data OTP sebelumnya tidak ditemukan. Silakan register ulang.',
                    ], 404);
                }

                $name = $previousOtp->name;
                $passwordHash = $previousOtp->password_hash;
            }

            OtpCode::where('email', $email)
                ->whereNull('verified_at')
                ->delete();

            $otp = (string) random_int(100000, 999999);
            $expiresAt = now()->addMinutes(10);

            OtpCode::create([
                'name' => $name,
                'email' => $email,
                'password_hash' => $passwordHash,
                'otp_hash' => Hash::make($otp),
                'expires_at' => $expiresAt,
                'verified_at' => null,
            ]);

            try {
                Redis::publish('otp_requested', json_encode([
                    'type' => 'otp_requested',
                    'email' => $email,
                    'name' => $name,
                    'otp' => $otp,
                    'expired_at' => $expiresAt->toDateTimeString(),
                ]));
            } catch (\Exception $e) {
                // Redis/email tidak menggagalkan pembuatan ulang OTP.
            }

            return response()->json([
                'success' => true,
                'message' => 'Kode OTP baru berhasil dikirim.',
                'data' => [
                    'email' => $email,
                    'expired_in_minutes' => 10,
                    'otp_debug' => config('app.debug') ? $otp : null,
                ],
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim ulang OTP',
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

    private function syncMemberProfile(User $user, array $profileData = []): array
    {
        try {
            $memberServiceUrl = rtrim(env('MEMBER_SERVICE_URL', 'http://member-service:8000'), '/');

            $payload = [
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ];

            foreach (['phone', 'address', 'status'] as $key) {
                if (array_key_exists($key, $profileData)) {
                    $payload[$key] = $profileData[$key];
                }
            }

            $response = Http::timeout(5)
                ->withHeaders([
                    'X-INTERNAL-SECRET' => env('INTERNAL_SERVICE_SECRET', 'retobluto_internal_secret'),
                    'Accept' => 'application/json',
                ])
                ->post($memberServiceUrl . '/api/internal/members/sync-from-auth', $payload);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'message' => 'Member Service gagal membuat atau memperbarui profil member',
                    'status' => $response->status(),
                    'response' => $response->json(),
                ];
            }

            return [
                'success' => true,
                'message' => 'Profil member berhasil disinkronkan di Member Service',
                'response' => $response->json(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Member Service unavailable',
                'error' => $e->getMessage(),
            ];
        }
    }

    private function createOtpForVerification(string $name, string $email, string $passwordHash): array
    {
        $otp = (string) random_int(100000, 999999);
        $expiresAt = now()->addMinutes(10);

        OtpCode::where('email', $email)
            ->whereNull('verified_at')
            ->delete();

        OtpCode::create([
            'name' => $name,
            'email' => $email,
            'password_hash' => $passwordHash,
            'otp_hash' => Hash::make($otp),
            'expires_at' => $expiresAt,
        ]);

        try {
            Redis::publish('otp_requested', json_encode([
                'type' => 'otp_requested',
                'email' => $email,
                'name' => $name,
                'otp' => $otp,
                'expired_at' => $expiresAt->toDateTimeString(),
            ]));
        } catch (\Exception $e) {
            //
        }

        return [
            'otp' => $otp,
            'expires_at' => $expiresAt,
        ];
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

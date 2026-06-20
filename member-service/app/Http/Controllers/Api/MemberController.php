<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class MemberController extends Controller
{
    private array $allowedStatuses = [
        'active',
        'inactive',
        'blocked',
    ];


    public function dashboardStats(Request $request)
    {
        $adminCheck = $this->ensureAdmin($request);

        if ($adminCheck !== true) {
            return $adminCheck;
        }

        try {
            return response()->json([
                'success' => true,
                'message' => 'Statistik dashboard member berhasil diambil',
                'data' => [
                    'total_members' => Member::count(),
                    'active_members' => Member::where('status', 'active')->count(),
                    'inactive_members' => Member::where('status', 'inactive')->count(),
                    'blocked_members' => Member::where('status', 'blocked')->count(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik dashboard member',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function index(Request $request)
    {
        $adminCheck = $this->ensureAdmin($request);

        if ($adminCheck !== true) {
            return $adminCheck;
        }

        try {
            $request->validate([
                'search' => 'nullable|string|max:100',
                'status' => ['nullable', Rule::in($this->allowedStatuses)],
            ]);

            $query = Member::query();

            if ($request->filled('search')) {
                $search = $request->query('search');

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%");
                });
            }

            if ($request->filled('status')) {
                $query->where('status', $request->query('status'));
            }

            $perPage = min((int) $request->query('per_page', 10), 50);
            $members = $query->latest()->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Data member berhasil diambil',
                'data' => $members,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi filter gagal',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data member',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $adminCheck = $this->ensureAdmin($request);

        if ($adminCheck !== true) {
            return $adminCheck;
        }

        try {
            $validated = $request->validate([
                'user_id' => 'nullable|integer|unique:members,user_id',
                'name' => 'required|string|max:100',
                'email' => 'required|email|max:150|unique:members,email',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string',
                'status' => ['nullable', Rule::in($this->allowedStatuses)],
            ]);

            $validated['status'] = $validated['status'] ?? 'active';

            $member = Member::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Member berhasil dibuat',
                'data' => $member,
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
                'message' => 'Gagal membuat member',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(Request $request, string $id)
    {
        $authCheck = $this->ensureAuthenticated($request);

        if (!is_array($authCheck)) {
            return $authCheck;
        }

        try {
            $member = Member::find($id);

            if (!$member) {
                return response()->json([
                    'success' => false,
                    'message' => 'Member tidak ditemukan',
                ], 404);
            }

            $user = $authCheck['user'];

            if (($user['role'] ?? null) !== 'admin' && (int) ($user['id'] ?? 0) !== (int) $member->user_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak. Anda hanya dapat melihat profil sendiri.',
                ], 403);
            }

            return response()->json([
                'success' => true,
                'message' => 'Detail member berhasil diambil',
                'data' => $member,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail member',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getByUserId(Request $request, string $userId)
    {
        $authCheck = $this->ensureAuthenticated($request);

        if (!is_array($authCheck)) {
            return $authCheck;
        }

        try {
            $member = Member::where('user_id', $userId)->first();

            if (!$member) {
                return response()->json([
                    'success' => false,
                    'message' => 'Member tidak ditemukan',
                ], 404);
            }

            $user = $authCheck['user'];

            if (($user['role'] ?? null) !== 'admin' && (int) ($user['id'] ?? 0) !== (int) $member->user_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak. Anda hanya dapat melihat profil sendiri.',
                ], 403);
            }

            return response()->json([
                'success' => true,
                'message' => 'Member berdasarkan user_id berhasil diambil',
                'data' => $member,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil member berdasarkan user_id',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function profile(Request $request)
    {
        $authCheck = $this->ensureAuthenticated($request);

        if (!is_array($authCheck)) {
            return $authCheck;
        }

        try {
            $user = $authCheck['user'];

            $member = Member::where('user_id', $user['id'])->first();

            if (!$member) {
                return response()->json([
                    'success' => false,
                    'message' => 'Profil member belum tersedia',
                    'user' => $user,
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Profil member berhasil diambil',
                'data' => $member,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil profil member',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateProfile(Request $request)
    {
        $authCheck = $this->ensureAuthenticated($request);

        if (!is_array($authCheck)) {
            return $authCheck;
        }

        try {
            $user = $authCheck['user'];

            if (($user['role'] ?? null) !== 'member') {
                return response()->json([
                    'success' => false,
                    'message' => 'Endpoint ini hanya untuk member.',
                ], 403);
            }

            $member = Member::where('user_id', $user['id'])->first();

            if (!$member) {
                return response()->json([
                    'success' => false,
                    'message' => 'Profil member belum tersedia',
                ], 404);
            }

            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:100',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string',
            ]);

            $member->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Profil member berhasil diperbarui',
                'data' => $member->fresh(),
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
                'message' => 'Gagal memperbarui profil member',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, string $id)
    {
        $adminCheck = $this->ensureAdmin($request);

        if ($adminCheck !== true) {
            return $adminCheck;
        }

        try {
            $member = Member::find($id);

            if (!$member) {
                return response()->json([
                    'success' => false,
                    'message' => 'Member tidak ditemukan',
                ], 404);
            }

            $validated = $request->validate([
                'user_id' => [
                    'nullable',
                    'integer',
                    Rule::unique('members', 'user_id')->ignore($member->id),
                ],
                'name' => 'required|string|max:100',
                'email' => [
                    'required',
                    'email',
                    'max:150',
                    Rule::unique('members', 'email')->ignore($member->id),
                ],
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string',
                'status' => ['required', Rule::in($this->allowedStatuses)],
            ]);

            $member->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Member berhasil diperbarui',
                'data' => $member->fresh(),
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
                'message' => 'Gagal memperbarui member',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateStatus(Request $request, string $id)
    {
        $adminCheck = $this->ensureAdmin($request);

        if ($adminCheck !== true) {
            return $adminCheck;
        }

        try {
            $member = Member::find($id);

            if (!$member) {
                return response()->json([
                    'success' => false,
                    'message' => 'Member tidak ditemukan',
                ], 404);
            }

            $validated = $request->validate([
                'status' => ['required', Rule::in($this->allowedStatuses)],
            ]);

            $member->update([
                'status' => $validated['status'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status member berhasil diperbarui',
                'data' => $member->fresh(),
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
                'message' => 'Gagal memperbarui status member',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Request $request, string $id)
    {
        $adminCheck = $this->ensureAdmin($request);

        if ($adminCheck !== true) {
            return $adminCheck;
        }

        try {
            $member = Member::find($id);

            if (!$member) {
                return response()->json([
                    'success' => false,
                    'message' => 'Member tidak ditemukan',
                ], 404);
            }

            $member->delete();

            return response()->json([
                'success' => true,
                'message' => 'Member berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus member',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function syncFromAuth(Request $request)
    {
        $internalCheck = $this->ensureInternalService($request);

        if ($internalCheck !== true) {
            return $internalCheck;
        }

        try {
            $validated = $request->validate([
                'user_id' => 'required|integer',
                'name' => 'required|string|max:100',
                'email' => 'required|email|max:150',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string',
                'status' => ['nullable', Rule::in($this->allowedStatuses)],
            ]);

            $member = Member::where('user_id', $validated['user_id'])->first();

            if ($member) {
                $updateData = [
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                ];

                if ($request->has('phone')) {
                    $updateData['phone'] = $validated['phone'] ?? null;
                }

                if ($request->has('address')) {
                    $updateData['address'] = $validated['address'] ?? null;
                }

                if ($request->has('status')) {
                    $updateData['status'] = $validated['status'];
                }

                $member->update($updateData);
            } else {
                $member = Member::create([
                    'user_id' => $validated['user_id'],
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'] ?? null,
                    'address' => $validated['address'] ?? null,
                    'status' => $validated['status'] ?? 'active',
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Profil member berhasil disinkronkan dari Auth Service',
                'data' => $member->fresh(),
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi sync member gagal',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal sinkronisasi member dari Auth Service',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function internalShow(Request $request, string $id)
    {
        $internalCheck = $this->ensureInternalService($request);

        if ($internalCheck !== true) {
            return $internalCheck;
        }

        $member = Member::find($id);

        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Member tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data member internal berhasil diambil',
            'data' => $member,
        ]);
    }

    public function internalGetByUserId(Request $request, string $userId)
    {
        $internalCheck = $this->ensureInternalService($request);

        if ($internalCheck !== true) {
            return $internalCheck;
        }

        $member = Member::where('user_id', $userId)->first();

        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Member tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data member internal berdasarkan user_id berhasil diambil',
            'data' => $member,
        ]);
    }

    private function ensureAuthenticated(Request $request)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak ditemukan',
            ], 401);
        }

        $cacheKey = 'auth_token:' . hash('sha256', $token);
        $cachedPayload = Cache::get($cacheKey);

        if (($cachedPayload['valid'] ?? false) && isset($cachedPayload['user'])) {
            $request->attributes->set('auth_payload', $cachedPayload);
            $request->attributes->set('auth_user', $cachedPayload['user']);

            return [
                'payload' => $cachedPayload,
                'user' => $cachedPayload['user'],
            ];
        }

        try {
            $authServiceUrl = rtrim(env('AUTH_SERVICE_URL', 'http://auth-service:8000'), '/');

            $response = Http::timeout(5)
                ->withToken($token)
                ->acceptJson()
                ->post($authServiceUrl . '/api/validate-token');

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token tidak valid',
                    'auth_service_status' => $response->status(),
                    'auth_service_response' => $response->json(),
                ], 401);
            }

            $payload = $response->json();

            if (!($payload['valid'] ?? false) || !isset($payload['user'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token tidak valid',
                ], 401);
            }

            Cache::put($cacheKey, $payload, now()->addSeconds(60));

            $request->attributes->set('auth_payload', $payload);
            $request->attributes->set('auth_user', $payload['user']);

            return [
                'payload' => $payload,
                'user' => $payload['user'],
            ];
        } catch (ConnectionException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Auth Service unavailable',
                'error' => $e->getMessage(),
            ], 503);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memvalidasi token',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function ensureAdmin(Request $request)
    {
        $authCheck = $this->ensureAuthenticated($request);

        if (!is_array($authCheck)) {
            return $authCheck;
        }

        $user = $authCheck['user'];

        if (($user['role'] ?? null) !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Endpoint ini hanya untuk admin.',
                'user_role' => $user['role'] ?? null,
            ], 403);
        }

        return true;
    }

    private function ensureInternalService(Request $request)
    {
        $secret = $request->header('X-INTERNAL-SECRET');

        if (!$secret || $secret !== env('INTERNAL_SERVICE_SECRET')) {
            return response()->json([
                'success' => false,
                'message' => 'Akses internal service ditolak.',
            ], 403);
        }

        return true;
    }
}

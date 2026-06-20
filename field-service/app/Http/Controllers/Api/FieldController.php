<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Field;
use Illuminate\Http\Request;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class FieldController extends Controller
{
    private array $allowedTypes = [
        'Futsal',
        'Badminton',
        'Basket',
        'Tenis',
        'Mini Soccer',
        'Voli',
    ];

    private array $allowedStatuses = [
        'available',
        'maintenance',
        'inactive',
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
                'message' => 'Statistik dashboard lapangan berhasil diambil',
                'data' => [
                    'total_fields' => Field::count(),
                    'available_fields' => Field::where('status', 'available')->count(),
                    'maintenance_fields' => Field::where('status', 'maintenance')->count(),
                    'inactive_fields' => Field::where('status', 'inactive')->count(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik dashboard lapangan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function index(Request $request)
    {
        try {
            $request->validate([
                'type' => ['nullable', Rule::in($this->allowedTypes)],
                'status' => ['nullable', Rule::in($this->allowedStatuses)],
                'search' => 'nullable|string|max:100',
            ]);

            $query = Field::query();

            if ($request->filled('type')) {
                $query->where('type', $request->query('type'));
            }

            if ($request->filled('status')) {
                $query->where('status', $request->query('status'));
            }

            if ($request->filled('search')) {
                $search = $request->query('search');

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('type', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%");
                });
            }

            $perPage = min((int) $request->query('per_page', 10), 50);
            $fields = $query->latest()->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Data lapangan berhasil diambil',
                'data' => $fields,
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
                'message' => 'Gagal mengambil data lapangan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function available()
    {
        try {
            $fields = Field::where('status', 'available')
                ->latest()
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Data lapangan tersedia berhasil diambil',
                'data' => $fields,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data lapangan tersedia',
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
                'name' => 'required|string|max:150|unique:fields,name',
                'type' => ['required', Rule::in($this->allowedTypes)],
                'description' => 'nullable|string',
                'location' => 'nullable|string|max:255',
                'price_per_hour' => 'required|numeric|min:0',
                'status' => ['nullable', Rule::in($this->allowedStatuses)],
                'open_time' => 'nullable|date_format:H:i',
                'close_time' => 'nullable|date_format:H:i|after:open_time',
            ]);

            $validated['status'] = $validated['status'] ?? 'available';

            $field = Field::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Lapangan berhasil dibuat',
                'data' => $field,
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
                'message' => 'Gagal membuat lapangan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(string $id)
    {
        try {
            $field = Field::find($id);

            if (!$field) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lapangan tidak ditemukan',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Detail lapangan berhasil diambil',
                'data' => $field,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail lapangan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function detail(string $id)
    {
        return $this->show($id);
    }

    public function update(Request $request, string $id)
    {
        $adminCheck = $this->ensureAdmin($request);

        if ($adminCheck !== true) {
            return $adminCheck;
        }

        try {
            $field = Field::find($id);

            if (!$field) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lapangan tidak ditemukan',
                ], 404);
            }

            $validated = $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:150',
                    Rule::unique('fields', 'name')->ignore($field->id),
                ],
                'type' => ['required', Rule::in($this->allowedTypes)],
                'description' => 'nullable|string',
                'location' => 'nullable|string|max:255',
                'price_per_hour' => 'required|numeric|min:0',
                'status' => ['required', Rule::in($this->allowedStatuses)],
                'open_time' => 'nullable|date_format:H:i',
                'close_time' => 'nullable|date_format:H:i|after:open_time',
            ]);

            $field->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Lapangan berhasil diperbarui',
                'data' => $field->fresh(),
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
                'message' => 'Gagal memperbarui lapangan',
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
            $field = Field::find($id);

            if (!$field) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lapangan tidak ditemukan',
                ], 404);
            }

            $validated = $request->validate([
                'status' => ['required', Rule::in($this->allowedStatuses)],
            ]);

            $field->update([
                'status' => $validated['status'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status lapangan berhasil diperbarui',
                'data' => $field->fresh(),
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
                'message' => 'Gagal memperbarui status lapangan',
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
            $field = Field::find($id);

            if (!$field) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lapangan tidak ditemukan',
                ], 404);
            }

            $field->delete();

            return response()->json([
                'success' => true,
                'message' => 'Lapangan berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus lapangan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function bookingSchedule(Request $request, string $id)
    {
        try {
            $field = Field::find($id);

            if (!$field) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lapangan tidak ditemukan',
                ], 404);
            }

            $request->validate([
                'date' => 'nullable|date_format:Y-m-d',
            ]);

            $bookingServiceUrl = rtrim(env('BOOKING_SERVICE_URL', 'http://booking-service:8000'), '/');

            $response = Http::timeout(5)->get($bookingServiceUrl . "/api/bookings/field/{$id}/schedule", [
                'date' => $request->query('date'),
            ]);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to get booking data',
                    'field' => $field,
                    'booking_service_status' => $response->status(),
                    'booking_service_response' => $response->json(),
                ], 502);
            }

            return response()->json([
                'success' => true,
                'message' => 'Jadwal booking lapangan berhasil diambil',
                'field' => $field,
                'data' => $response->json('data', []),
                'booking_schedule' => $response->json(),
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);
        } catch (ConnectionException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Booking Service unavailable',
                'error' => $e->getMessage(),
            ], 503);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil jadwal booking lapangan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function ensureAdmin(Request $request)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak ditemukan',
            ], 401);
        }

        $cacheKey = 'auth_token:' . hash('sha256', $token);
        $payload = Cache::get($cacheKey);

        try {
            if (!($payload['valid'] ?? false) || !isset($payload['user'])) {
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
                Cache::put($cacheKey, $payload, now()->addSeconds(60));
            }

            $user = $payload['user'] ?? null;

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data user tidak ditemukan dari Auth Service',
                ], 401);
            }

            $request->attributes->set('auth_payload', $payload);
            $request->attributes->set('auth_user', $user);

            if (($user['role'] ?? null) !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak. Endpoint ini hanya untuk admin.',
                    'user_role' => $user['role'] ?? null,
                ], 403);
            }

            return true;
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
}

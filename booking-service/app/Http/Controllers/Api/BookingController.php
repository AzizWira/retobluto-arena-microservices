<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    private array $activeStatuses = [
        'pending',
        'approved',
    ];

    private array $historyStatuses = [
        'pending',
        'approved',
        'rejected',
    ];

    private array $allStatuses = [
        'pending',
        'approved',
        'rejected',
        'canceled',
    ];

    public function health()
    {
        return response()->json([
            'success' => true,
            'service' => 'booking-service',
            'message' => 'Booking Service is running',
        ]);
    }

    public function index(Request $request)
    {
        $adminCheck = $this->ensureAdmin($request);

        if ($adminCheck !== true) {
            return $adminCheck;
        }

        try {
            $request->validate([
                'status' => ['nullable', Rule::in($this->allStatuses)],
                'booking_date' => 'nullable|date_format:Y-m-d',
                'field_id' => 'nullable|integer',
                'member_id' => 'nullable|integer',
                'search' => 'nullable|string|max:100',
            ]);

            $query = Booking::query();

            if ($request->filled('status')) {
                $query->where('status', $request->query('status'));
            }

            if ($request->filled('booking_date')) {
                $query->whereDate('booking_date', $request->query('booking_date'));
            }

            if ($request->filled('field_id')) {
                $query->where('field_id', $request->query('field_id'));
            }

            if ($request->filled('member_id')) {
                $query->where('member_id', $request->query('member_id'));
            }

            if ($request->filled('search')) {
                $search = $request->query('search');

                $query->where(function ($q) use ($search) {
                    $q->where('member_name', 'like', "%{$search}%")
                        ->orWhere('member_email', 'like', "%{$search}%")
                        ->orWhere('field_name', 'like', "%{$search}%")
                        ->orWhere('field_type', 'like', "%{$search}%");
                });
            }

            $bookings = $query->latest()->get();

            return response()->json([
                'success' => true,
                'message' => 'Data booking berhasil diambil',
                'data' => $bookings,
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
                'message' => 'Gagal mengambil data booking',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $authCheck = $this->ensureAuthenticated($request);

        if (!is_array($authCheck)) {
            return $authCheck;
        }

        $user = $authCheck['user'];

        if (($user['role'] ?? null) !== 'member') {
            return response()->json([
                'success' => false,
                'message' => 'Endpoint ini hanya untuk member.',
                'user_role' => $user['role'] ?? null,
            ], 403);
        }

        try {
            $validated = $request->validate([
                'field_id' => 'required|integer',
                'booking_date' => 'required|date_format:Y-m-d|after_or_equal:today',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'note' => 'nullable|string',
            ]);

            $memberResponse = $this->getMemberByUserId($user['id']);

            if (!$memberResponse['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data member tidak ditemukan',
                    'member_service' => $memberResponse,
                ], 404);
            }

            $member = $memberResponse['data'];

            if (($member['status'] ?? null) !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Member tidak aktif sehingga tidak dapat melakukan booking.',
                    'member_status' => $member['status'] ?? null,
                ], 403);
            }

            $fieldResponse = $this->getField($validated['field_id']);

            if (!$fieldResponse['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data lapangan tidak ditemukan atau Field Service bermasalah',
                    'field_service' => $fieldResponse,
                ], 404);
            }

            $field = $fieldResponse['data'];

            if (($field['status'] ?? null) !== 'available') {
                return response()->json([
                    'success' => false,
                    'message' => 'Lapangan tidak tersedia untuk booking.',
                    'field_status' => $field['status'] ?? null,
                ], 422);
            }

            $timeValidation = $this->validateBookingTimeAgainstField(
                $validated['start_time'],
                $validated['end_time'],
                $field
            );

            if ($timeValidation !== true) {
                return $timeValidation;
            }

            $activeSameDate = Booking::where('member_id', $member['id'])
                ->whereDate('booking_date', $validated['booking_date'])
                ->whereIn('status', $this->activeStatuses)
                ->exists();

            if ($activeSameDate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Member sudah memiliki booking aktif pada tanggal yang sama.',
                ], 422);
            }

            $hasScheduleConflict = $this->hasFieldScheduleConflict(
                fieldId: (int) $field['id'],
                bookingDate: $validated['booking_date'],
                startTime: $validated['start_time'],
                endTime: $validated['end_time']
            );

            if ($hasScheduleConflict) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jadwal booking bentrok dengan booking lain.',
                ], 422);
            }

            $durationHours = $this->calculateDurationHours(
                $validated['start_time'],
                $validated['end_time']
            );

            $pricePerHour = (float) $field['price_per_hour'];
            $totalPrice = $durationHours * $pricePerHour;

            $booking = Booking::create([
                'member_id' => $member['id'],
                'member_user_id' => $user['id'],
                'member_name' => $member['name'],
                'member_email' => $member['email'],
                'field_id' => $field['id'],
                'field_name' => $field['name'],
                'field_type' => $field['type'],
                'booking_date' => $validated['booking_date'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'duration_hours' => $durationHours,
                'price_per_hour' => $pricePerHour,
                'total_price' => $totalPrice,
                'status' => 'pending',
                'note' => $validated['note'] ?? null,
            ]);

            $this->publishBookingEvent('booking_created', $booking);

            return response()->json([
                'success' => true,
                'message' => 'Booking berhasil dibuat dan menunggu persetujuan admin.',
                'data' => $booking,
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
                'message' => 'Gagal membuat booking',
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
            $booking = Booking::find($id);

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking tidak ditemukan',
                ], 404);
            }

            $user = $authCheck['user'];

            if (($user['role'] ?? null) !== 'admin' && (int) $booking->member_user_id !== (int) $user['id']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak. Anda hanya dapat melihat booking sendiri.',
                ], 403);
            }

            return response()->json([
                'success' => true,
                'message' => 'Detail booking berhasil diambil',
                'data' => $booking,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail booking',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function memberBookings(Request $request)
    {
        $authCheck = $this->ensureAuthenticated($request);

        if (!is_array($authCheck)) {
            return $authCheck;
        }

        $user = $authCheck['user'];

        if (($user['role'] ?? null) !== 'member') {
            return response()->json([
                'success' => false,
                'message' => 'Endpoint ini hanya untuk member.',
                'user_role' => $user['role'] ?? null,
            ], 403);
        }

        try {
            $bookings = Booking::where('member_user_id', $user['id'])
                ->whereIn('status', ['pending', 'approved'])
                ->latest()
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Booking aktif member berhasil diambil',
                'data' => $bookings,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil booking aktif member',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function memberHistory(Request $request)
    {
        $authCheck = $this->ensureAuthenticated($request);

        if (!is_array($authCheck)) {
            return $authCheck;
        }

        $user = $authCheck['user'];

        if (($user['role'] ?? null) !== 'member') {
            return response()->json([
                'success' => false,
                'message' => 'Endpoint ini hanya untuk member.',
                'user_role' => $user['role'] ?? null,
            ], 403);
        }

        try {
            $bookings = Booking::where('member_user_id', $user['id'])
                ->whereIn('status', $this->historyStatuses)
                ->latest()
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'History booking member berhasil diambil',
                'data' => $bookings,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil history booking member',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function adminRequests(Request $request)
    {
        $adminCheck = $this->ensureAdmin($request);

        if ($adminCheck !== true) {
            return $adminCheck;
        }

        try {
            $bookings = Booking::where('status', 'pending')
                ->latest()
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Request booking pending berhasil diambil',
                'data' => $bookings,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil request booking',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function approve(Request $request, string $id)
    {
        $adminCheck = $this->ensureAdmin($request);

        if ($adminCheck !== true) {
            return $adminCheck;
        }

        $authCheck = $this->ensureAuthenticated($request);
        $adminUser = $authCheck['user'] ?? null;

        try {
            $booking = Booking::find($id);

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking tidak ditemukan',
                ], 404);
            }

            if ($booking->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya booking pending yang dapat di-approve.',
                    'current_status' => $booking->status,
                ], 422);
            }

            $hasApprovedConflict = Booking::where('id', '!=', $booking->id)
                ->where('field_id', $booking->field_id)
                ->whereDate('booking_date', $booking->booking_date)
                ->where('status', 'approved')
                ->where(function ($q) use ($booking) {
                    $q->where('start_time', '<', $booking->end_time)
                        ->where('end_time', '>', $booking->start_time);
                })
                ->exists();

            if ($hasApprovedConflict) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking tidak dapat di-approve karena jadwal sudah terisi.',
                ], 422);
            }

            $booking->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => $adminUser['id'] ?? null,
            ]);

            $this->publishBookingEvent('booking_approved', $booking->fresh());

            return response()->json([
                'success' => true,
                'message' => 'Booking berhasil di-approve dan otomatis menjadi jadwal resmi.',
                'data' => $booking->fresh(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal approve booking',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function reject(Request $request, string $id)
    {
        $adminCheck = $this->ensureAdmin($request);

        if ($adminCheck !== true) {
            return $adminCheck;
        }

        $authCheck = $this->ensureAuthenticated($request);
        $adminUser = $authCheck['user'] ?? null;

        try {
            $validated = $request->validate([
                'rejection_reason' => 'nullable|string',
            ]);

            $booking = Booking::find($id);

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking tidak ditemukan',
                ], 404);
            }

            if ($booking->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya booking pending yang dapat di-reject.',
                    'current_status' => $booking->status,
                ], 422);
            }

            $booking->update([
                'status' => 'rejected',
                'rejection_reason' => $validated['rejection_reason'] ?? null,
                'rejected_at' => now(),
                'rejected_by' => $adminUser['id'] ?? null,
            ]);

            $this->publishBookingEvent('booking_rejected', $booking->fresh());

            return response()->json([
                'success' => true,
                'message' => 'Booking berhasil di-reject.',
                'data' => $booking->fresh(),
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
                'message' => 'Gagal reject booking',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function cancel(Request $request, string $id)
    {
        $authCheck = $this->ensureAuthenticated($request);

        if (!is_array($authCheck)) {
            return $authCheck;
        }

        $user = $authCheck['user'];

        if (($user['role'] ?? null) !== 'member') {
            return response()->json([
                'success' => false,
                'message' => 'Endpoint ini hanya untuk member.',
                'user_role' => $user['role'] ?? null,
            ], 403);
        }

        try {
            $booking = Booking::find($id);

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking tidak ditemukan',
                ], 404);
            }

            if ((int) $booking->member_user_id !== (int) $user['id']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak. Anda hanya dapat membatalkan booking sendiri.',
                ], 403);
            }

            if ($booking->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya booking pending yang dapat dibatalkan oleh member.',
                    'current_status' => $booking->status,
                ], 422);
            }

            $booking->update([
                'status' => 'canceled',
                'canceled_at' => now(),
                'canceled_by' => $user['id'],
            ]);

            $this->publishBookingEvent('booking_canceled', $booking->fresh());

            return response()->json([
                'success' => true,
                'message' => 'Booking berhasil dibatalkan. Booking tidak akan tampil lagi pada daftar aktif.',
                'data' => $booking->fresh(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan booking',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function fieldSchedule(Request $request, string $fieldId)
    {
        try {
            $request->validate([
                'date' => 'nullable|date_format:Y-m-d',
                'status' => ['nullable', Rule::in($this->allStatuses)],
            ]);

            $query = Booking::where('field_id', $fieldId);

            if ($request->filled('date')) {
                $query->whereDate('booking_date', $request->query('date'));
            }

            if ($request->filled('status')) {
                $query->where('status', $request->query('status'));
            } else {
                $query->where('status', 'approved');
            }

            $bookings = $query
                ->orderBy('booking_date')
                ->orderBy('start_time')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Jadwal booking lapangan berhasil diambil',
                'data' => $bookings,
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
                'message' => 'Gagal mengambil jadwal booking lapangan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function bookingsByMember(Request $request, string $memberId)
    {
        $adminCheck = $this->ensureAdmin($request);

        if ($adminCheck !== true) {
            return $adminCheck;
        }

        $bookings = Booking::where('member_id', $memberId)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data booking berdasarkan member berhasil diambil',
            'data' => $bookings,
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

        try {
            $authServiceUrl = rtrim(env('AUTH_SERVICE_URL', 'http://auth-service:8000'), '/');

            $response = Http::timeout(15)
                ->connectTimeout(5)
                ->retry(2, 300)
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

            return [
                'payload' => $payload,
                'user' => $payload['user'],
                'token' => $token,
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

    private function getMemberByUserId(int $userId): array
    {
        try {
            $memberServiceUrl = rtrim(env('MEMBER_SERVICE_URL', 'http://member-service:8000'), '/');

            $response = Http::timeout(15)
                ->connectTimeout(5)
                ->retry(2, 300)
                ->withHeaders([
                    'X-INTERNAL-SECRET' => env('INTERNAL_SERVICE_SECRET', 'retobluto_internal_secret'),
                    'Accept' => 'application/json',
                ])
                ->get($memberServiceUrl . "/api/internal/members/user/{$userId}");

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'status' => $response->status(),
                    'response' => $response->json(),
                ];
            }

            $payload = $response->json();

            return [
                'success' => true,
                'data' => $payload['data'],
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Member Service unavailable',
                'error' => $e->getMessage(),
            ];
        }
    }

    private function getField(int $fieldId): array
    {
        try {
            $fieldServiceUrl = rtrim(env('FIELD_SERVICE_URL', 'http://field-service:8000'), '/');

            $response = Http::timeout(15)
                ->connectTimeout(5)
                ->retry(2, 300)
                ->acceptJson()
                ->get($fieldServiceUrl . "/api/fields/{$fieldId}");

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'status' => $response->status(),
                    'response' => $response->json(),
                ];
            }

            $payload = $response->json();

            return [
                'success' => true,
                'data' => $payload['data'],
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Field Service unavailable',
                'error' => $e->getMessage(),
            ];
        }
    }

    private function validateBookingTimeAgainstField(string $startTime, string $endTime, array $field)
    {
        $openTime = $field['open_time'] ?? null;
        $closeTime = $field['close_time'] ?? null;

        if (!$openTime || !$closeTime) {
            return true;
        }

        if ($startTime < substr($openTime, 0, 5) || $endTime > substr($closeTime, 0, 5)) {
            return response()->json([
                'success' => false,
                'message' => 'Jam booking berada di luar jam operasional lapangan.',
                'field_open_time' => substr($openTime, 0, 5),
                'field_close_time' => substr($closeTime, 0, 5),
            ], 422);
        }

        return true;
    }

    private function calculateDurationHours(string $startTime, string $endTime): int
    {
        $start = strtotime($startTime);
        $end = strtotime($endTime);

        $durationSeconds = $end - $start;
        $durationHours = (int) ceil($durationSeconds / 3600);

        return max($durationHours, 1);
    }

    private function hasFieldScheduleConflict(
        int $fieldId,
        string $bookingDate,
        string $startTime,
        string $endTime
    ): bool {
        return Booking::where('field_id', $fieldId)
            ->whereDate('booking_date', $bookingDate)
            ->whereIn('status', $this->activeStatuses)
            ->where(function ($q) use ($startTime, $endTime) {
                $q->where('start_time', '<', $endTime)
                    ->where('end_time', '>', $startTime);
            })
            ->exists();
    }

    private function publishBookingEvent(string $event, Booking $booking): void
    {
        $payload = [
            'type' => $event,
            'booking_id' => $booking->id,
            'recipient_email' => $booking->member_email,
            'member_name' => $booking->member_name,
            'field_name' => $booking->field_name,
            'field_type' => $booking->field_type,
            'booking_date' => optional($booking->booking_date)->format('Y-m-d') ?? (string) $booking->booking_date,
            'start_time' => substr($booking->start_time, 0, 5),
            'end_time' => substr($booking->end_time, 0, 5),
            'status' => $booking->status,
            'total_price' => $booking->total_price,
        ];

        try {
            Redis::publish($event, json_encode($payload));
        } catch (\Exception $e) {
            // Redis event tidak menggagalkan proses booking.
        }
    }
}

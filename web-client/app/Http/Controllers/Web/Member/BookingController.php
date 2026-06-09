<?php

namespace App\Http\Controllers\Web\Member;

use App\Http\Controllers\Web\BaseWebController;
use Illuminate\Http\Request;

class BookingController extends BaseWebController
{
    public function index(Request $request)
    {
        if ($redirect = $this->requireMember()) {
            return $redirect;
        }

        $activeBookings = [];
        $historyBookings = [];

        try {
            $response = $this->bookingClient()->get('/api/member/bookings');
            $activeBookings = $response->successful() ? $this->dataList($response) : [];
        } catch (\Exception $e) {
            $activeBookings = [];
        }

        try {
            $response = $this->bookingClient()->get('/api/member/bookings/history');
            $historyBookings = $response->successful() ? $this->dataList($response) : [];
        } catch (\Exception $e) {
            $historyBookings = [];
        }

        $bookings = collect($activeBookings)
            ->merge($historyBookings)
            ->unique('id')
            ->sortByDesc('id')
            ->values();

        if ($request->filled('status')) {
            $bookings = $bookings->where('status', $request->status)->values();
        }

        if ($request->filled('booking_date')) {
            $bookings = $bookings
                ->filter(function ($booking) use ($request) {
                    return isset($booking['booking_date'])
                        && substr($booking['booking_date'], 0, 10) === $request->booking_date;
                })
                ->values();
        }

        if ($request->filled('search')) {
            $search = strtolower($request->search);

            $bookings = $bookings
                ->filter(function ($booking) use ($search) {
                    return str_contains(strtolower($booking['field_name'] ?? ''), $search)
                        || str_contains(strtolower($booking['field_type'] ?? ''), $search);
                })
                ->values();
        }

        return view('member.bookings.index', [
            'bookings' => $bookings->all(),
        ]);
    }

    public function create(Request $request)
    {
        if ($redirect = $this->requireMember()) {
            return $redirect;
        }

        $fields = [];
        $selectedField = null;
        $selectedFieldId = $request->field_id;

        try {
            $response = $this->fieldClient()->get('/api/fields/available');
            $fields = $response->successful() ? $this->dataList($response) : [];
        } catch (\Exception $e) {
            $fields = [];
        }

        if ($selectedFieldId) {
            try {
                $response = $this->fieldClient()->get("/api/fields/{$selectedFieldId}/detail");

                if (!$response->successful()) {
                    $response = $this->fieldClient()->get("/api/fields/{$selectedFieldId}");
                }

                $selectedField = $response->successful() ? $this->dataItem($response) : null;
            } catch (\Exception $e) {
                $selectedField = null;
            }
        }

        return view('member.bookings.create', compact(
            'fields',
            'selectedField',
            'selectedFieldId'
        ));
    }

    public function store(Request $request)
    {
        if ($redirect = $this->requireMember()) {
            return $redirect;
        }

        $validated = $request->validate([
            'field_id' => ['required', 'integer'],
            'booking_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'note' => ['nullable', 'string'],
        ]);

        try {
            $response = $this->bookingClient()->post('/api/bookings', $validated);
        } catch (\Exception $e) {
            $createdBooking = $this->findSubmittedBooking($validated);

            if ($createdBooking && !empty($createdBooking['id'])) {
                return redirect()
                    ->route('member.bookings.show', $createdBooking['id'])
                    ->with('success', 'Booking berhasil dibuat. Response sempat lambat, tetapi data booking sudah tersimpan.');
            }

            return back()
                ->withInput()
                ->with('error', 'Booking Service tidak dapat dihubungi atau response terlalu lama. Silakan cek menu Booking Saya sebelum submit ulang.');
        }

        if (!$response->successful()) {
            return back()
                ->withInput()
                ->with('error', $this->apiError($response, 'Gagal membuat booking.'));
        }

        $booking = $this->dataItem($response);

        if (!$booking || empty($booking['id'])) {
            $createdBooking = $this->findSubmittedBooking($validated);

            if ($createdBooking && !empty($createdBooking['id'])) {
                return redirect()
                    ->route('member.bookings.show', $createdBooking['id'])
                    ->with('success', 'Booking berhasil dibuat.');
            }

            return redirect()
                ->route('member.bookings.index')
                ->with('success', 'Booking berhasil dibuat. Silakan cek daftar Booking Saya.');
        }

        return redirect()
            ->route('member.bookings.show', $booking['id'])
            ->with('success', 'Booking berhasil dibuat dan menunggu approval admin.');
    }

    public function show($id)
    {
        if ($redirect = $this->requireMember()) {
            return $redirect;
        }

        try {
            $response = $this->bookingClient()->get("/api/bookings/{$id}");
        } catch (\Exception $e) {
            return redirect()
                ->route('member.bookings.index')
                ->with('error', 'Booking Service tidak dapat dihubungi.');
        }

        if (!$response->successful()) {
            return redirect()
                ->route('member.bookings.index')
                ->with('error', $this->apiError($response, 'Data booking tidak ditemukan.'));
        }

        $booking = $this->dataItem($response);

        return view('member.bookings.show', compact('booking'));
    }

    public function cancel($id)
    {
        if ($redirect = $this->requireMember()) {
            return $redirect;
        }

        try {
            $response = $this->bookingClient()->post("/api/member/bookings/{$id}/cancel");
        } catch (\Exception $e) {
            return back()->with('error', 'Booking Service tidak dapat dihubungi.');
        }

        if (!$response->successful()) {
            return back()->with('error', $this->apiError($response, 'Gagal membatalkan booking.'));
        }

        return redirect()
            ->route('member.bookings.index')
            ->with('success', 'Booking berhasil dibatalkan.');
    }

    private function findSubmittedBooking(array $submitted): ?array
    {
        $activeBookings = [];
        $historyBookings = [];

        try {
            $response = $this->bookingClient()->get('/api/member/bookings');
            $activeBookings = $response->successful() ? $this->dataList($response) : [];
        } catch (\Exception $e) {
            $activeBookings = [];
        }

        try {
            $response = $this->bookingClient()->get('/api/member/bookings/history');
            $historyBookings = $response->successful() ? $this->dataList($response) : [];
        } catch (\Exception $e) {
            $historyBookings = [];
        }

        return collect($activeBookings)
            ->merge($historyBookings)
            ->filter(function ($booking) use ($submitted) {
                $sameField = (string) ($booking['field_id'] ?? '') === (string) ($submitted['field_id'] ?? '');
                $sameDate = substr($booking['booking_date'] ?? '', 0, 10) === ($submitted['booking_date'] ?? '');
                $sameStart = substr($booking['start_time'] ?? '', 0, 5) === ($submitted['start_time'] ?? '');
                $sameEnd = substr($booking['end_time'] ?? '', 0, 5) === ($submitted['end_time'] ?? '');

                return $sameField && $sameDate && $sameStart && $sameEnd;
            })
            ->sortByDesc('id')
            ->first();
    }
}

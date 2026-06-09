<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Web\BaseWebController;
use Illuminate\Http\Request;

class BookingController extends BaseWebController
{
    public function index(Request $request)
    {
        if ($redirect = $this->requireAdmin()) {
            return $redirect;
        }

        try {
            $response = $this->bookingClient()->get('/api/bookings', $this->onlyFilled([
                'status' => $request->status,
                'booking_date' => $request->booking_date,
                'field_id' => $request->field_id,
                'member_id' => $request->member_id,
                'search' => $request->search,
            ]));

            $bookings = $response->successful() ? $this->dataList($response) : [];
        } catch (\Exception $e) {
            $bookings = [];

            return view('admin.bookings.index', compact('bookings'))
                ->with('error', 'Booking Service tidak dapat dihubungi.');
        }

        return view('admin.bookings.index', compact('bookings'));
    }

    public function requests()
    {
        if ($redirect = $this->requireAdmin()) {
            return $redirect;
        }

        try {
            $response = $this->bookingClient()->get('/api/admin/booking-requests');
            $bookings = $response->successful() ? $this->dataList($response) : [];
        } catch (\Exception $e) {
            $bookings = [];

            return view('admin.bookings.requests', compact('bookings'))
                ->with('error', 'Booking Service tidak dapat dihubungi.');
        }

        return view('admin.bookings.requests', compact('bookings'));
    }

    public function show($id)
    {
        if ($redirect = $this->requireAdmin()) {
            return $redirect;
        }

        try {
            $response = $this->bookingClient()->get("/api/bookings/{$id}");
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.bookings.index')
                ->with('error', 'Booking Service tidak dapat dihubungi.');
        }

        if (!$response->successful()) {
            return redirect()
                ->route('admin.bookings.index')
                ->with('error', $this->apiError($response, 'Data booking tidak ditemukan.'));
        }

        $booking = $this->dataItem($response);

        return view('admin.bookings.show', compact('booking'));
    }

    public function approve($id)
    {
        if ($redirect = $this->requireAdmin()) {
            return $redirect;
        }

        try {
            $response = $this->bookingClient()->post("/api/admin/bookings/{$id}/approve");
        } catch (\Exception $e) {
            return back()->with('error', 'Booking Service tidak dapat dihubungi.');
        }

        if (!$response->successful()) {
            return back()->with('error', $this->apiError($response, 'Gagal approve booking.'));
        }

        return back()->with('success', 'Booking berhasil di-approve dan masuk ke jadwal lapangan.');
    }

    public function reject(Request $request, $id)
    {
        if ($redirect = $this->requireAdmin()) {
            return $redirect;
        }

        $validated = $request->validate([
            'rejection_reason' => ['nullable', 'string'],
        ]);

        try {
            $response = $this->bookingClient()->post("/api/admin/bookings/{$id}/reject", [
                'rejection_reason' => $validated['rejection_reason'] ?? null,
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Booking Service tidak dapat dihubungi.');
        }

        if (!$response->successful()) {
            return back()->with('error', $this->apiError($response, 'Gagal reject booking.'));
        }

        return back()->with('success', 'Booking berhasil di-reject.');
    }
}

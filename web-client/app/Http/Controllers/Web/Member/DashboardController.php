<?php

namespace App\Http\Controllers\Web\Member;

use App\Http\Controllers\Web\BaseWebController;

class DashboardController extends BaseWebController
{
    public function index()
    {
        if ($redirect = $this->requireMember()) {
            return $redirect;
        }

        $profile = null;
        $activeBookings = [];
        $historyBookings = [];
        $fields = [];

        try {
            $response = $this->memberClient()->get('/api/profile');
            $profile = $response->successful() ? $this->dataItem($response) : null;
        } catch (\Exception $e) {
            $profile = null;
        }

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

        try {
            $response = $this->fieldClient()->get('/api/fields/available');
            $fields = $response->successful() ? $this->dataList($response) : [];
        } catch (\Exception $e) {
            $fields = [];
        }

        $allBookings = collect($activeBookings)
            ->merge($historyBookings)
            ->unique('id')
            ->values();

        $stats = [
            'available_fields' => count($fields),
            'total_bookings' => $allBookings->count(),
            'pending_bookings' => $allBookings->where('status', 'pending')->count(),
            'approved_bookings' => $allBookings->where('status', 'approved')->count(),
            'rejected_bookings' => $allBookings->where('status', 'rejected')->count(),
            'canceled_bookings' => $allBookings->where('status', 'canceled')->count(),
        ];

        $latestBookings = $allBookings
            ->sortByDesc('id')
            ->take(5)
            ->values()
            ->all();

        $recommendedFields = collect($fields)
            ->take(6)
            ->values()
            ->all();

        return view('member.home', compact(
            'profile',
            'stats',
            'latestBookings',
            'recommendedFields'
        ));
    }
}

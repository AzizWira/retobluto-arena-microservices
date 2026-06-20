<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Web\BaseWebController;

class DashboardController extends BaseWebController
{
    public function index()
    {
        if ($redirect = $this->requireAdmin()) {
            return $redirect;
        }

        $stats = [
            'total_fields' => 0,
            'available_fields' => 0,
            'maintenance_fields' => 0,
            'inactive_fields' => 0,

            'total_members' => 0,
            'active_members' => 0,
            'inactive_members' => 0,
            'blocked_members' => 0,

            'total_bookings' => 0,
            'pending_bookings' => 0,
            'approved_bookings' => 0,
            'rejected_bookings' => 0,
            'canceled_bookings' => 0,

            'notification_logs' => 0,
        ];

        $latestBookings = [];
        $pendingBookings = [];

        try {
            $response = $this->fieldClient(5)->get('/api/fields/dashboard-stats');

            if ($response->successful()) {
                $stats = array_merge($stats, $this->dataItem($response) ?? []);
            }
        } catch (\Exception $e) {
            // Tetap tampilkan dashboard dengan nilai default.
        }

        try {
            $response = $this->memberClient(5)->get('/api/members/dashboard-stats');

            if ($response->successful()) {
                $stats = array_merge($stats, $this->dataItem($response) ?? []);
            }
        } catch (\Exception $e) {
            // Tetap tampilkan dashboard dengan nilai default.
        }

        try {
            $response = $this->bookingClient(5)->get('/api/bookings/dashboard-stats');

            if ($response->successful()) {
                $bookingData = $this->dataItem($response) ?? [];
                $stats = array_merge($stats, $bookingData['stats'] ?? []);
                $latestBookings = $bookingData['latest_bookings'] ?? [];
                $pendingBookings = $bookingData['pending_bookings'] ?? [];
            }
        } catch (\Exception $e) {
            // Tetap tampilkan dashboard dengan nilai default.
        }

        try {
            $response = $this->notificationClient(5)->get('/api/notifications/dashboard-stats');

            if ($response->successful()) {
                $stats = array_merge($stats, $this->dataItem($response) ?? []);
            }
        } catch (\Exception $e) {
            // Tetap tampilkan dashboard dengan nilai default.
        }

        return view('admin.dashboard', compact(
            'stats',
            'latestBookings',
            'pendingBookings'
        ));
    }
}

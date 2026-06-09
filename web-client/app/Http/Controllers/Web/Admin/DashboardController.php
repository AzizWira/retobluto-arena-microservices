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

        $fields = [];
        $members = [];
        $bookings = [];
        $pendingBookings = [];
        $notificationLogs = [];

        try {
            $response = $this->fieldClient()->get('/api/fields');
            $fields = $response->successful() ? $this->dataList($response) : [];
        } catch (\Exception $e) {
            //
        }

        try {
            $response = $this->memberClient()->get('/api/members');
            $members = $response->successful() ? $this->dataList($response) : [];
        } catch (\Exception $e) {
            //
        }

        try {
            $response = $this->bookingClient()->get('/api/bookings');
            $bookings = $response->successful() ? $this->dataList($response) : [];
        } catch (\Exception $e) {
            //
        }

        try {
            $response = $this->bookingClient()->get('/api/admin/booking-requests');
            $pendingBookings = $response->successful() ? $this->dataList($response) : [];
        } catch (\Exception $e) {
            //
        }

        try {
            $response = $this->notificationClient()->get('/api/notifications/logs');
            $notificationLogs = $response->successful() ? $this->dataList($response) : [];
        } catch (\Exception $e) {
            //
        }

        $stats = [
            'total_fields' => count($fields),
            'available_fields' => collect($fields)->where('status', 'available')->count(),
            'maintenance_fields' => collect($fields)->where('status', 'maintenance')->count(),
            'inactive_fields' => collect($fields)->where('status', 'inactive')->count(),

            'total_members' => count($members),
            'active_members' => collect($members)->where('status', 'active')->count(),
            'inactive_members' => collect($members)->where('status', 'inactive')->count(),
            'blocked_members' => collect($members)->where('status', 'blocked')->count(),

            'total_bookings' => count($bookings),
            'pending_bookings' => collect($bookings)->where('status', 'pending')->count(),
            'approved_bookings' => collect($bookings)->where('status', 'approved')->count(),
            'rejected_bookings' => collect($bookings)->where('status', 'rejected')->count(),
            'canceled_bookings' => collect($bookings)->where('status', 'canceled')->count(),

            'notification_logs' => count($notificationLogs),
        ];

        $latestBookings = collect($bookings)
            ->sortByDesc('id')
            ->take(10)
            ->values()
            ->all();

        return view('admin.dashboard', compact(
            'stats',
            'latestBookings',
            'pendingBookings'
        ));
    }
}

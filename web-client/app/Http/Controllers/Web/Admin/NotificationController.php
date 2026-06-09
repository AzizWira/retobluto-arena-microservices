<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Web\BaseWebController;
use Illuminate\Http\Request;

class NotificationController extends BaseWebController
{
    public function index(Request $request)
    {
        if ($redirect = $this->requireAdmin()) {
            return $redirect;
        }

        try {
            $response = $this->notificationClient()->get('/api/notifications/logs', $this->onlyFilled([
                'type' => $request->type,
                'status' => $request->status,
                'search' => $request->search,
            ]));

            $logs = $response->successful() ? $this->dataList($response) : [];
        } catch (\Exception $e) {
            $logs = [];

            return view('admin.notifications.index', compact('logs'))
                ->with('error', 'Notification Service tidak dapat dihubungi.');
        }

        return view('admin.notifications.index', compact('logs'));
    }

    public function show($id)
    {
        if ($redirect = $this->requireAdmin()) {
            return $redirect;
        }

        try {
            $response = $this->notificationClient()->get("/api/notifications/logs/{$id}");
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.notifications.index')
                ->with('error', 'Notification Service tidak dapat dihubungi.');
        }

        if (!$response->successful()) {
            return redirect()
                ->route('admin.notifications.index')
                ->with('error', $this->apiError($response, 'Data notifikasi tidak ditemukan.'));
        }

        $log = $this->dataItem($response);

        return view('admin.notifications.show', compact('log'));
    }

    public function createEmail()
    {
        if ($redirect = $this->requireAdmin()) {
            return $redirect;
        }

        return view('admin.notifications.send-email');
    }

    public function sendEmail(Request $request)
    {
        if ($redirect = $this->requireAdmin()) {
            return $redirect;
        }

        $validated = $request->validate([
            'recipient_email' => ['required', 'email', 'max:150'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
        ]);

        try {
            $response = $this->notificationClient()->post('/api/notifications/send-email', $validated);
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Notification Service tidak dapat dihubungi.');
        }

        if (!$response->successful()) {
            return back()
                ->withInput()
                ->with('error', $this->apiError($response, 'Gagal mengirim email.'));
        }

        $log = $this->dataItem($response);

        return redirect()
            ->route('admin.notifications.show', $log['id'] ?? null)
            ->with('success', 'Email berhasil diproses.');
    }
}

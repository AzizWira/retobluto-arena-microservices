<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Web\BaseWebController;
use Illuminate\Http\Request;

class MemberController extends BaseWebController
{
    public function index(Request $request)
    {
        if ($redirect = $this->requireAdmin()) {
            return $redirect;
        }

        try {
            $response = $this->memberClient()->get('/api/members', $this->onlyFilled([
                'search' => $request->search,
                'status' => $request->status,
            ]));

            $members = $response->successful() ? $this->dataList($response) : [];
        } catch (\Exception $e) {
            $members = [];

            return view('admin.members.index', compact('members'))
                ->with('error', 'Member Service tidak dapat dihubungi.');
        }

        return view('admin.members.index', compact('members'));
    }

    public function create()
    {
        if ($redirect = $this->requireAdmin()) {
            return $redirect;
        }

        return view('admin.members.create');
    }

    public function store(Request $request)
    {
        if ($redirect = $this->requireAdmin()) {
            return $redirect;
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'password' => ['required', 'string', 'min:6'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
        ]);

        try {
            $response = $this->authClient()
                ->withToken($this->token())
                ->post('/api/admin/members', $validated);
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Auth Service tidak dapat dihubungi.');
        }

        if (!$response->successful()) {
            return back()
                ->withInput()
                ->with('error', $this->apiError($response, 'Gagal menambahkan member.'));
        }

        $memberId = $response->json('data.member_sync.response.data.id');
        $otpDebug = $response->json('data.otp_debug');

        $message = 'Member berhasil dibuat. Status awal inactive dan member perlu verifikasi OTP sebelum bisa booking.';

        if ($otpDebug) {
            $message .= ' OTP Debug: ' . $otpDebug;
        }

        if ($memberId) {
            return redirect()
                ->route('admin.members.show', $memberId)
                ->with('success', $message);
        }

        return redirect()
            ->route('admin.members.index')
            ->with('success', $message);
    }

    public function show($id)
    {
        if ($redirect = $this->requireAdmin()) {
            return $redirect;
        }

        try {
            $response = $this->memberClient()->get("/api/members/{$id}");
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.members.index')
                ->with('error', 'Member Service tidak dapat dihubungi.');
        }

        if (!$response->successful()) {
            return redirect()
                ->route('admin.members.index')
                ->with('error', $this->apiError($response, 'Data member tidak ditemukan.'));
        }

        $member = $this->dataItem($response);
        $bookings = [];

        try {
            $bookingResponse = $this->bookingClient()->get("/api/bookings/member/{$id}");
            $bookings = $bookingResponse->successful() ? $this->dataList($bookingResponse) : [];
        } catch (\Exception $e) {
            $bookings = [];
        }

        return view('admin.members.show', compact('member', 'bookings'));
    }

    public function edit($id)
    {
        if ($redirect = $this->requireAdmin()) {
            return $redirect;
        }

        try {
            $response = $this->memberClient()->get("/api/members/{$id}");
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.members.index')
                ->with('error', 'Member Service tidak dapat dihubungi.');
        }

        if (!$response->successful()) {
            return redirect()
                ->route('admin.members.index')
                ->with('error', $this->apiError($response, 'Data member tidak ditemukan.'));
        }

        $member = $this->dataItem($response);

        return view('admin.members.edit', compact('member'));
    }

    public function update(Request $request, $id)
    {
        if ($redirect = $this->requireAdmin()) {
            return $redirect;
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive,blocked'],
        ]);

        try {
            $response = $this->memberClient()->put("/api/members/{$id}", $validated);
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Member Service tidak dapat dihubungi.');
        }

        if (!$response->successful()) {
            return back()
                ->withInput()
                ->with('error', $this->apiError($response, 'Gagal memperbarui member.'));
        }

        return redirect()
            ->route('admin.members.show', $id)
            ->with('success', 'Data member berhasil diperbarui.');
    }

    public function updateStatus(Request $request, $id)
    {
        if ($redirect = $this->requireAdmin()) {
            return $redirect;
        }

        $validated = $request->validate([
            'status' => ['required', 'in:active,inactive,blocked'],
        ]);

        try {
            $response = $this->memberClient()->patch("/api/members/{$id}/status", $validated);
        } catch (\Exception $e) {
            return back()->with('error', 'Member Service tidak dapat dihubungi.');
        }

        if (!$response->successful()) {
            return back()->with('error', $this->apiError($response, 'Gagal memperbarui status member.'));
        }

        return back()->with('success', 'Status member berhasil diperbarui.');
    }

    public function destroy($id)
    {
        if ($redirect = $this->requireAdmin()) {
            return $redirect;
        }

        try {
            $detailResponse = $this->memberClient()->get("/api/members/{$id}");
        } catch (\Exception $e) {
            return back()->with('error', 'Member Service tidak dapat dihubungi.');
        }

        if (!$detailResponse->successful()) {
            return back()->with('error', $this->apiError($detailResponse, 'Data member tidak ditemukan.'));
        }

        $member = $this->dataItem($detailResponse);

        $authPayload = [
            'user_id' => $member['user_id'] ?? null,
            'email' => $member['email'] ?? null,
        ];

        try {
            $authResponse = $this->authClient()
                ->withToken($this->token())
                ->delete('/api/admin/members/auth-account', $authPayload);
        } catch (\Exception $e) {
            return back()->with('error', 'Auth Service tidak dapat dihubungi. Member belum dihapus agar data tidak menjadi tidak sinkron.');
        }

        if (!$authResponse->successful()) {
            return back()->with('error', $this->apiError($authResponse, 'Gagal menghapus akun member dari Auth Service.'));
        }

        try {
            $response = $this->memberClient()->delete("/api/members/{$id}");
        } catch (\Exception $e) {
            return back()->with('error', 'Akun Auth sudah terhapus, tetapi Member Service tidak dapat dihubungi. Silakan hapus data member lagi.');
        }

        if (!$response->successful()) {
            return back()->with('error', $this->apiError($response, 'Gagal menghapus data member.'));
        }

        return redirect()
            ->route('admin.members.index')
            ->with('success', 'Member berhasil dihapus dari Member Service dan Auth Service.');
    }
}

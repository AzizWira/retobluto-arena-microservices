<?php

namespace App\Http\Controllers\Web\Member;

use App\Http\Controllers\Web\BaseWebController;
use Illuminate\Http\Request;

class ProfileController extends BaseWebController
{
    public function show()
    {
        if ($redirect = $this->requireMember()) {
            return $redirect;
        }

        try {
            $response = $this->memberClient()->get('/api/profile');
        } catch (\Exception $e) {
            return redirect()
                ->route('member.home')
                ->with('error', 'Member Service tidak dapat dihubungi.');
        }

        if (!$response->successful()) {
            return redirect()
                ->route('member.home')
                ->with('error', $this->apiError($response, 'Profil member tidak ditemukan.'));
        }

        $profile = $this->dataItem($response);

        return view('member.profile.show', compact('profile'));
    }

    public function edit()
    {
        if ($redirect = $this->requireMember()) {
            return $redirect;
        }

        try {
            $response = $this->memberClient()->get('/api/profile');
        } catch (\Exception $e) {
            return redirect()
                ->route('member.home')
                ->with('error', 'Member Service tidak dapat dihubungi.');
        }

        if (!$response->successful()) {
            return redirect()
                ->route('member.home')
                ->with('error', $this->apiError($response, 'Profil member tidak ditemukan.'));
        }

        $profile = $this->dataItem($response);

        return view('member.profile.edit', compact('profile'));
    }

    public function update(Request $request)
    {
        if ($redirect = $this->requireMember()) {
            return $redirect;
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
        ]);

        try {
            $response = $this->memberClient()->put('/api/profile', $validated);
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Member Service tidak dapat dihubungi.');
        }

        if (!$response->successful()) {
            return back()
                ->withInput()
                ->with('error', $this->apiError($response, 'Gagal memperbarui profil.'));
        }

        return redirect()
            ->route('member.profile.show')
            ->with('success', 'Profil berhasil diperbarui.');
    }
}

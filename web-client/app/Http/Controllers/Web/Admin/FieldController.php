<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Web\BaseWebController;
use Illuminate\Http\Request;

class FieldController extends BaseWebController
{
    public function index(Request $request)
    {
        if ($redirect = $this->requireAdmin()) {
            return $redirect;
        }

        try {
            $response = $this->fieldClient()->get('/api/fields', $this->onlyFilled([
                'search' => $request->search,
                'type' => $request->type,
                'status' => $request->status,
            ]));

            $fields = $response->successful() ? $this->dataList($response) : [];
        } catch (\Exception $e) {
            $fields = [];

            return view('admin.fields.index', compact('fields'))
                ->with('error', 'Field Service tidak dapat dihubungi.');
        }

        return view('admin.fields.index', compact('fields'));
    }

    public function create()
    {
        if ($redirect = $this->requireAdmin()) {
            return $redirect;
        }

        return view('admin.fields.create');
    }

    public function store(Request $request)
    {
        if ($redirect = $this->requireAdmin()) {
            return $redirect;
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'type' => ['required', 'in:Futsal,Badminton,Basket,Tenis,Mini Soccer,Voli'],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'price_per_hour' => ['required', 'numeric', 'min:0'],
            'status' => ['nullable', 'in:available,maintenance,inactive'],
            'open_time' => ['nullable', 'date_format:H:i'],
            'close_time' => ['nullable', 'date_format:H:i', 'after:open_time'],
        ]);

        try {
            $response = $this->fieldClient()->post('/api/fields', $validated);
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Field Service tidak dapat dihubungi.');
        }

        if (!$response->successful()) {
            return back()
                ->withInput()
                ->with('error', $this->apiError($response, 'Gagal menambahkan lapangan.'));
        }

        $field = $this->dataItem($response);

        return redirect()
            ->route('admin.fields.show', $field['id'] ?? null)
            ->with('success', 'Lapangan berhasil ditambahkan.');
    }

    public function show(Request $request, $id)
    {
        if ($redirect = $this->requireAdmin()) {
            return $redirect;
        }

        try {
            $response = $this->fieldClient()->get("/api/fields/{$id}/detail");

            if (!$response->successful()) {
                $response = $this->fieldClient()->get("/api/fields/{$id}");
            }
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.fields.index')
                ->with('error', 'Field Service tidak dapat dihubungi.');
        }

        if (!$response->successful()) {
            return redirect()
                ->route('admin.fields.index')
                ->with('error', $this->apiError($response, 'Data lapangan tidak ditemukan.'));
        }

        $field = $this->dataItem($response);

        $selectedDate = $request->date ?: now()->format('Y-m-d');
        $selectedStatus = $request->status ?: null;
        $schedules = [];

        try {
            $scheduleResponse = $this->fieldClient()->get("/api/fields/{$id}/booking-schedule", $this->onlyFilled([
                'date' => $selectedDate,
                'status' => $selectedStatus,
            ]));

            $schedules = $scheduleResponse->successful()
                ? $this->dataList($scheduleResponse)
                : [];
        } catch (\Exception $e) {
            $schedules = [];
        }

        return view('admin.fields.show', compact(
            'field',
            'schedules',
            'selectedDate',
            'selectedStatus'
        ));
    }

    public function edit($id)
    {
        if ($redirect = $this->requireAdmin()) {
            return $redirect;
        }

        try {
            $response = $this->fieldClient()->get("/api/fields/{$id}/detail");

            if (!$response->successful()) {
                $response = $this->fieldClient()->get("/api/fields/{$id}");
            }
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.fields.index')
                ->with('error', 'Field Service tidak dapat dihubungi.');
        }

        if (!$response->successful()) {
            return redirect()
                ->route('admin.fields.index')
                ->with('error', $this->apiError($response, 'Data lapangan tidak ditemukan.'));
        }

        $field = $this->dataItem($response);

        return view('admin.fields.edit', compact('field'));
    }

    public function update(Request $request, $id)
    {
        if ($redirect = $this->requireAdmin()) {
            return $redirect;
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'type' => ['required', 'in:Futsal,Badminton,Basket,Tenis,Mini Soccer,Voli'],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'price_per_hour' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:available,maintenance,inactive'],
            'open_time' => ['nullable', 'date_format:H:i'],
            'close_time' => ['nullable', 'date_format:H:i', 'after:open_time'],
        ]);

        try {
            $response = $this->fieldClient()->put("/api/fields/{$id}", $validated);
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Field Service tidak dapat dihubungi.');
        }

        if (!$response->successful()) {
            return back()
                ->withInput()
                ->with('error', $this->apiError($response, 'Gagal memperbarui lapangan.'));
        }

        return redirect()
            ->route('admin.fields.show', $id)
            ->with('success', 'Lapangan berhasil diperbarui.');
    }

    public function updateStatus(Request $request, $id)
    {
        if ($redirect = $this->requireAdmin()) {
            return $redirect;
        }

        $validated = $request->validate([
            'status' => ['required', 'in:available,maintenance,inactive'],
        ]);

        try {
            $response = $this->fieldClient()->patch("/api/fields/{$id}/status", $validated);
        } catch (\Exception $e) {
            return back()->with('error', 'Field Service tidak dapat dihubungi.');
        }

        if (!$response->successful()) {
            return back()->with('error', $this->apiError($response, 'Gagal memperbarui status lapangan.'));
        }

        return back()->with('success', 'Status lapangan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        if ($redirect = $this->requireAdmin()) {
            return $redirect;
        }

        try {
            $response = $this->fieldClient()->delete("/api/fields/{$id}");
        } catch (\Exception $e) {
            return back()->with('error', 'Field Service tidak dapat dihubungi.');
        }

        if (!$response->successful()) {
            return back()->with('error', $this->apiError($response, 'Gagal menghapus lapangan.'));
        }

        return redirect()
            ->route('admin.fields.index')
            ->with('success', 'Lapangan berhasil dihapus.');
    }
}

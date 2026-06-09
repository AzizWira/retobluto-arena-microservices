<?php

namespace App\Http\Controllers\Web\Member;

use App\Http\Controllers\Web\BaseWebController;
use Illuminate\Http\Request;

class FieldController extends BaseWebController
{
    public function index(Request $request)
    {
        if ($redirect = $this->requireMember()) {
            return $redirect;
        }

        try {
            $response = $this->fieldClient()->get('/api/fields', $this->onlyFilled([
                'search' => $request->search,
                'type' => $request->type,
                'status' => 'available',
            ]));

            $fields = $response->successful() ? $this->dataList($response) : [];
        } catch (\Exception $e) {
            $fields = [];

            return view('member.fields.index', compact('fields'))
                ->with('error', 'Field Service tidak dapat dihubungi.');
        }

        return view('member.fields.index', compact('fields'));
    }

    public function show(Request $request, $id)
    {
        if ($redirect = $this->requireMember()) {
            return $redirect;
        }

        try {
            $response = $this->fieldClient()->get("/api/fields/{$id}/detail");

            if (!$response->successful()) {
                $response = $this->fieldClient()->get("/api/fields/{$id}");
            }
        } catch (\Exception $e) {
            return redirect()
                ->route('member.fields.index')
                ->with('error', 'Field Service tidak dapat dihubungi.');
        }

        if (!$response->successful()) {
            return redirect()
                ->route('member.fields.index')
                ->with('error', $this->apiError($response, 'Data lapangan tidak ditemukan.'));
        }

        $field = $this->dataItem($response);

        $selectedDate = $request->date ?: now()->format('Y-m-d');
        $schedules = [];

        try {
            $scheduleResponse = $this->bookingClient()->get("/api/bookings/field/{$id}/schedule", [
                'date' => $selectedDate,
                'status' => 'approved',
            ]);

            $schedules = $scheduleResponse->successful()
                ? $this->dataList($scheduleResponse)
                : [];
        } catch (\Exception $e) {
            $schedules = [];
        }

        return view('member.fields.show', compact(
            'field',
            'schedules',
            'selectedDate'
        ));
    }
}

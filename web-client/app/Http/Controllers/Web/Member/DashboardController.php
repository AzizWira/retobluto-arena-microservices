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

        $personalRecommendedFields = $this->buildPersonalRecommendedFields(
            fields: $fields,
            bookings: $allBookings
        );

        $globalPopularFields = $this->buildGlobalPopularFields($fields);

        return view('member.home', compact(
            'profile',
            'stats',
            'latestBookings',
            'personalRecommendedFields',
            'globalPopularFields'
        ));
    }


    private function buildPersonalRecommendedFields(array $fields, $bookings): array
    {
        $bookingHistory = collect($bookings)
            ->filter(function ($booking) {
                return !empty($booking['field_id']) || !empty($booking['field_type']);
            })
            ->values();

        $bookedFieldIds = $bookingHistory
            ->pluck('field_id')
            ->filter()
            ->map(fn($fieldId) => (int) $fieldId)
            ->unique()
            ->values();

        $favoriteTypes = $bookingHistory
            ->pluck('field_type')
            ->filter()
            ->map(fn($type) => strtolower(trim($type)))
            ->countBy()
            ->sortDesc();

        return collect($fields)
            ->map(function ($field) use ($bookedFieldIds, $favoriteTypes) {
                $score = 0;
                $reason = null;

                $fieldId = (int) ($field['id'] ?? 0);
                $fieldType = strtolower(trim($field['type'] ?? ''));

                if ($fieldId && $bookedFieldIds->contains($fieldId)) {
                    $score += 100;
                    $reason = 'Pernah kamu booking';
                }

                if ($fieldType !== '' && $favoriteTypes->has($fieldType)) {
                    $score += ((int) $favoriteTypes->get($fieldType)) * 10;
                    $reason ??= 'Sesuai tipe favorit kamu';
                }

                $field['_recommendation_score'] = $score;
                $field['_recommendation_reason'] = $reason;

                return $field;
            })
            ->filter(fn($field) => ($field['_recommendation_score'] ?? 0) > 0)
            ->sortByDesc('_recommendation_score')
            ->take(3)
            ->map(function ($field) {
                unset($field['_recommendation_score']);

                return $field;
            })
            ->values()
            ->all();
    }

    private function buildGlobalPopularFields(array $fields): array
    {
        $popularFieldStats = [];

        try {
            $response = $this->bookingClient()->get('/api/bookings/popular-fields', [
                'limit' => 20,
            ]);

            $popularFieldStats = $response->successful() ? $this->dataList($response) : [];
        } catch (\Exception $e) {
            $popularFieldStats = [];
        }

        $availableFieldsById = collect($fields)
            ->keyBy(fn($field) => (int) ($field['id'] ?? 0));

        return collect($popularFieldStats)
            ->map(function ($popularField) use ($availableFieldsById) {
                $fieldId = (int) ($popularField['field_id'] ?? 0);

                if (!$fieldId || !$availableFieldsById->has($fieldId)) {
                    return null;
                }

                $field = $availableFieldsById->get($fieldId);
                $field['_booking_count'] = (int) ($popularField['booking_count'] ?? 0);

                return $field;
            })
            ->filter()
            ->take(3)
            ->values()
            ->all();
    }
}

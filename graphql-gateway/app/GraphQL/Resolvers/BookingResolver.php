<?php

namespace App\GraphQL\Resolvers;

use App\GraphQL\Core\GraphQLParser;
use App\GraphQL\Services\BookingServiceClient;

class BookingResolver
{
    private array $allowedStatuses = [
        'pending',
        'approved',
        'rejected',
        'canceled',
    ];

    public function __construct(
        private readonly BookingServiceClient $bookingService
    ) {}

    public function bookings(string $query, array $context): array
    {
        $args = GraphQLParser::args($query, 'bookings');

        $validation = $this->validateBookingFilters($args);

        if ($validation !== true) {
            return $this->listValidationError($validation);
        }

        $result = $this->bookingService->all([
            'status' => $args['status'] ?? null,
            'booking_date' => $args['booking_date'] ?? null,
            'field_id' => $args['field_id'] ?? null,
            'member_id' => $args['member_id'] ?? null,
            'search' => $args['search'] ?? null,
        ], $context['authorization'] ?? null);

        return $this->listPayload($result);
    }

    public function booking(string $query, array $context): array
    {
        $args = GraphQLParser::args($query, 'booking');

        $errors = [];

        if (!$this->validPositiveId($args['id'] ?? null)) {
            $errors['id'][] = 'Argument id wajib berupa angka lebih dari 0.';
        }

        if (!empty($errors)) {
            return $this->singleValidationError($errors);
        }

        $result = $this->bookingService->find(
            id: $args['id'],
            authorization: $context['authorization'] ?? null
        );

        return $this->singlePayload($result);
    }

    public function myBookings(array $context): array
    {
        if (empty($context['authorization'])) {
            return $this->listValidationError([
                'authorization' => ['Token member wajib diisi untuk melihat booking aktif.'],
            ]);
        }

        $result = $this->bookingService->memberBookings($context['authorization']);

        return $this->listPayload($result);
    }

    public function myBookingHistory(array $context): array
    {
        if (empty($context['authorization'])) {
            return $this->listValidationError([
                'authorization' => ['Token member wajib diisi untuk melihat riwayat booking.'],
            ]);
        }

        $result = $this->bookingService->memberHistory($context['authorization']);

        return $this->listPayload($result);
    }

    public function bookingRequests(array $context): array
    {
        if (empty($context['authorization'])) {
            return $this->listValidationError([
                'authorization' => ['Token admin wajib diisi untuk melihat request booking.'],
            ]);
        }

        $result = $this->bookingService->adminRequests($context['authorization']);

        return $this->listPayload($result);
    }

    public function fieldBookingSchedule(string $query): array
    {
        $args = GraphQLParser::args($query, 'fieldBookingSchedule');

        $errors = [];

        if (!$this->validPositiveId($args['field_id'] ?? null)) {
            $errors['field_id'][] = 'Argument field_id wajib berupa angka lebih dari 0.';
        }

        if (isset($args['date']) && !$this->validDate($args['date'])) {
            $errors['date'][] = 'Date harus berformat YYYY-MM-DD.';
        }

        if (isset($args['status']) && !in_array($args['status'], $this->allowedStatuses, true)) {
            $errors['status'][] = 'Status hanya boleh: ' . implode(', ', $this->allowedStatuses) . '.';
        }

        if (!empty($errors)) {
            return $this->listValidationError($errors);
        }

        $result = $this->bookingService->fieldSchedule($args['field_id'], [
            'date' => $args['date'] ?? null,
            'status' => $args['status'] ?? null,
        ]);

        return $this->listPayload($result);
    }

    public function bookingsByMember(string $query, array $context): array
    {
        $args = GraphQLParser::args($query, 'bookingsByMember');

        $errors = [];

        if (!$this->validPositiveId($args['member_id'] ?? null)) {
            $errors['member_id'][] = 'Argument member_id wajib berupa angka lebih dari 0.';
        }

        if (empty($context['authorization'])) {
            $errors['authorization'][] = 'Token admin wajib diisi untuk melihat booking berdasarkan member.';
        }

        if (!empty($errors)) {
            return $this->listValidationError($errors);
        }

        $result = $this->bookingService->byMember(
            memberId: $args['member_id'],
            authorization: $context['authorization']
        );

        return $this->listPayload($result);
    }

    public function createBooking(string $query, array $context): array
    {
        $args = GraphQLParser::args($query, 'createBooking');

        $validation = $this->validateCreateBookingPayload($args, $context);

        if ($validation !== true) {
            return $this->mutationValidationError($validation);
        }

        $result = $this->bookingService->create([
            'field_id' => $args['field_id'],
            'booking_date' => $args['booking_date'],
            'start_time' => $args['start_time'],
            'end_time' => $args['end_time'],
            'note' => $args['note'] ?? null,
        ], $context['authorization']);

        return $this->mutationPayload($result);
    }

    public function approveBooking(string $query, array $context): array
    {
        $args = GraphQLParser::args($query, 'approveBooking');

        $errors = [];

        if (!$this->validPositiveId($args['id'] ?? null)) {
            $errors['id'][] = 'Argument id wajib berupa angka lebih dari 0.';
        }

        if (empty($context['authorization'])) {
            $errors['authorization'][] = 'Token admin wajib diisi untuk approve booking.';
        }

        if (!empty($errors)) {
            return $this->mutationValidationError($errors);
        }

        $result = $this->bookingService->approve(
            id: $args['id'],
            authorization: $context['authorization']
        );

        return $this->mutationPayload($result);
    }

    public function rejectBooking(string $query, array $context): array
    {
        $args = GraphQLParser::args($query, 'rejectBooking');

        $errors = [];

        if (!$this->validPositiveId($args['id'] ?? null)) {
            $errors['id'][] = 'Argument id wajib berupa angka lebih dari 0.';
        }

        if (empty($context['authorization'])) {
            $errors['authorization'][] = 'Token admin wajib diisi untuk reject booking.';
        }

        if (isset($args['rejection_reason']) && strlen((string) $args['rejection_reason']) > 500) {
            $errors['rejection_reason'][] = 'Alasan reject maksimal 500 karakter.';
        }

        if (!empty($errors)) {
            return $this->mutationValidationError($errors);
        }

        $result = $this->bookingService->reject(
            id: $args['id'],
            authorization: $context['authorization'],
            reason: $args['rejection_reason'] ?? null
        );

        return $this->mutationPayload($result);
    }

    public function cancelBooking(string $query, array $context): array
    {
        $args = GraphQLParser::args($query, 'cancelBooking');

        $errors = [];

        if (!$this->validPositiveId($args['id'] ?? null)) {
            $errors['id'][] = 'Argument id wajib berupa angka lebih dari 0.';
        }

        if (empty($context['authorization'])) {
            $errors['authorization'][] = 'Token member wajib diisi untuk cancel booking.';
        }

        if (!empty($errors)) {
            return $this->mutationValidationError($errors);
        }

        $result = $this->bookingService->cancel(
            id: $args['id'],
            authorization: $context['authorization']
        );

        return $this->mutationPayload($result);
    }

    private function validateBookingFilters(array $args): bool|array
    {
        $errors = [];

        if (isset($args['status']) && !in_array($args['status'], $this->allowedStatuses, true)) {
            $errors['status'][] = 'Status hanya boleh: ' . implode(', ', $this->allowedStatuses) . '.';
        }

        if (isset($args['booking_date']) && !$this->validDate($args['booking_date'])) {
            $errors['booking_date'][] = 'Booking date harus berformat YYYY-MM-DD.';
        }

        if (isset($args['field_id']) && !$this->validPositiveId($args['field_id'])) {
            $errors['field_id'][] = 'Field id harus berupa angka lebih dari 0.';
        }

        if (isset($args['member_id']) && !$this->validPositiveId($args['member_id'])) {
            $errors['member_id'][] = 'Member id harus berupa angka lebih dari 0.';
        }

        if (isset($args['search']) && strlen((string) $args['search']) > 100) {
            $errors['search'][] = 'Search maksimal 100 karakter.';
        }

        return empty($errors) ? true : $errors;
    }

    private function validateCreateBookingPayload(array $args, array $context): bool|array
    {
        $errors = [];

        if (empty($context['authorization'])) {
            $errors['authorization'][] = 'Token member wajib diisi untuk membuat booking.';
        }

        if (!$this->validPositiveId($args['field_id'] ?? null)) {
            $errors['field_id'][] = 'Field id wajib berupa angka lebih dari 0.';
        }

        if (empty($args['booking_date'])) {
            $errors['booking_date'][] = 'Tanggal booking wajib diisi.';
        } elseif (!$this->validDate($args['booking_date'])) {
            $errors['booking_date'][] = 'Tanggal booking harus berformat YYYY-MM-DD.';
        } elseif ($args['booking_date'] < date('Y-m-d')) {
            $errors['booking_date'][] = 'Tanggal booking tidak boleh sebelum hari ini.';
        }

        if (empty($args['start_time'])) {
            $errors['start_time'][] = 'Jam mulai wajib diisi.';
        } elseif (!$this->validTime($args['start_time'])) {
            $errors['start_time'][] = 'Jam mulai harus berformat HH:mm, contoh 08:00.';
        }

        if (empty($args['end_time'])) {
            $errors['end_time'][] = 'Jam selesai wajib diisi.';
        } elseif (!$this->validTime($args['end_time'])) {
            $errors['end_time'][] = 'Jam selesai harus berformat HH:mm, contoh 10:00.';
        }

        if (
            isset($args['start_time'], $args['end_time'])
            && $this->validTime($args['start_time'])
            && $this->validTime($args['end_time'])
            && $args['end_time'] <= $args['start_time']
        ) {
            $errors['end_time'][] = 'Jam selesai harus lebih besar dari jam mulai.';
        }

        if (isset($args['note']) && strlen((string) $args['note']) > 500) {
            $errors['note'][] = 'Catatan maksimal 500 karakter.';
        }

        return empty($errors) ? true : $errors;
    }

    private function validPositiveId(mixed $value): bool
    {
        return is_numeric($value) && (int) $value > 0;
    }

    private function validDate(string $date): bool
    {
        $parsed = date_create_from_format('Y-m-d', $date);

        return $parsed && $parsed->format('Y-m-d') === $date;
    }

    private function validTime(string $time): bool
    {
        return preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $time) === 1;
    }

    private function listValidationError(array $errors): array
    {
        return [
            'success' => false,
            'message' => 'Validasi GraphQL Gateway gagal.',
            'bookings' => [],
            'errors' => $errors,
        ];
    }

    private function singleValidationError(array $errors): array
    {
        return [
            'success' => false,
            'message' => 'Validasi GraphQL Gateway gagal.',
            'booking' => null,
            'errors' => $errors,
        ];
    }

    private function mutationValidationError(array $errors): array
    {
        return [
            'success' => false,
            'message' => 'Validasi GraphQL Gateway gagal.',
            'booking' => null,
            'errors' => $errors,
        ];
    }

    private function listPayload(array $result): array
    {
        return [
            'success' => (bool) ($result['success'] ?? false),
            'message' => $result['message'] ?? 'Request booking selesai.',
            'bookings' => is_array($result['data'] ?? null) ? $result['data'] : [],
            'errors' => $result['errors'] ?? null,
        ];
    }

    private function singlePayload(array $result): array
    {
        return [
            'success' => (bool) ($result['success'] ?? false),
            'message' => $result['message'] ?? 'Request booking selesai.',
            'booking' => is_array($result['data'] ?? null) ? $result['data'] : null,
            'errors' => $result['errors'] ?? null,
        ];
    }

    private function mutationPayload(array $result): array
    {
        return [
            'success' => (bool) ($result['success'] ?? false),
            'message' => $result['message'] ?? 'Request booking selesai.',
            'booking' => is_array($result['data'] ?? null) ? $result['data'] : null,
            'errors' => $result['errors'] ?? null,
        ];
    }
}

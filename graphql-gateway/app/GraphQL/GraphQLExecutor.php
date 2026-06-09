<?php

namespace App\GraphQL;

use App\GraphQL\Core\GraphQLParser;
use App\GraphQL\Resolvers\BookingResolver;
use App\GraphQL\Resolvers\FieldResolver;
use App\GraphQL\Resolvers\HealthResolver;

class GraphQLExecutor
{
    public function __construct(
        private readonly HealthResolver $healthResolver,
        private readonly FieldResolver $fieldResolver,
        private readonly BookingResolver $bookingResolver
    ) {}

    public function execute(string $query, array $variables = [], array $context = []): array
    {
        $data = [];
        $errors = [];

        if (GraphQLParser::hasField($query, 'health')) {
            $result = $this->healthResolver->resolve();
            $selectedFields = GraphQLParser::selectedFields($query, 'health');

            $data['health'] = GraphQLParser::filterSelection($result, $selectedFields);
        }

        if (GraphQLParser::hasField($query, 'fields')) {
            $result = $this->fieldResolver->fields($query);
            $selectedFields = GraphQLParser::selectedFields($query, 'fields');

            $data['fields'] = GraphQLParser::filterSelection($result, $selectedFields);
        }

        if (GraphQLParser::hasField($query, 'availableFields')) {
            $result = $this->fieldResolver->availableFields();
            $selectedFields = GraphQLParser::selectedFields($query, 'availableFields');

            $data['availableFields'] = GraphQLParser::filterSelection($result, $selectedFields);
        }

        if (GraphQLParser::hasField($query, 'fieldSchedule')) {
            $data['fieldSchedule'] = $this->fieldResolver->fieldSchedule($query);
        }

        if (GraphQLParser::hasField($query, 'field')) {
            $result = $this->fieldResolver->field($query);
            $selectedFields = GraphQLParser::selectedFields($query, 'field');

            $data['field'] = GraphQLParser::filterSelection($result, $selectedFields);
        }

        if (GraphQLParser::hasField($query, 'bookingsByMember')) {
            $result = $this->bookingResolver->bookingsByMember($query, $context);
            $selectedFields = GraphQLParser::selectedFields($query, 'bookingsByMember');

            $data['bookingsByMember'] = GraphQLParser::filterSelection($result, $selectedFields);
        }

        if (GraphQLParser::hasField($query, 'bookingRequests')) {
            $result = $this->bookingResolver->bookingRequests($context);
            $selectedFields = GraphQLParser::selectedFields($query, 'bookingRequests');

            $data['bookingRequests'] = GraphQLParser::filterSelection($result, $selectedFields);
        }

        if (GraphQLParser::hasField($query, 'myBookingHistory')) {
            $result = $this->bookingResolver->myBookingHistory($context);
            $selectedFields = GraphQLParser::selectedFields($query, 'myBookingHistory');

            $data['myBookingHistory'] = GraphQLParser::filterSelection($result, $selectedFields);
        }

        if (GraphQLParser::hasField($query, 'myBookings')) {
            $result = $this->bookingResolver->myBookings($context);
            $selectedFields = GraphQLParser::selectedFields($query, 'myBookings');

            $data['myBookings'] = GraphQLParser::filterSelection($result, $selectedFields);
        }

        if (GraphQLParser::hasField($query, 'fieldBookingSchedule')) {
            $result = $this->bookingResolver->fieldBookingSchedule($query);
            $selectedFields = GraphQLParser::selectedFields($query, 'fieldBookingSchedule');

            $data['fieldBookingSchedule'] = GraphQLParser::filterSelection($result, $selectedFields);
        }

        if (GraphQLParser::hasField($query, 'bookings')) {
            $result = $this->bookingResolver->bookings($query, $context);
            $selectedFields = GraphQLParser::selectedFields($query, 'bookings');

            $data['bookings'] = GraphQLParser::filterSelection($result, $selectedFields);
        }

        if (GraphQLParser::hasField($query, 'booking')) {
            $result = $this->bookingResolver->booking($query, $context);
            $selectedFields = GraphQLParser::selectedFields($query, 'booking');

            $data['booking'] = GraphQLParser::filterSelection($result, $selectedFields);
        }

        if (GraphQLParser::hasField($query, 'createField')) {
            $result = $this->fieldResolver->createField($query, $context);
            $selectedFields = GraphQLParser::selectedFields($query, 'createField');

            $data['createField'] = GraphQLParser::filterSelection($result, $selectedFields);
        }

        if (GraphQLParser::hasField($query, 'updateFieldStatus')) {
            $result = $this->fieldResolver->updateFieldStatus($query, $context);
            $selectedFields = GraphQLParser::selectedFields($query, 'updateFieldStatus');

            $data['updateFieldStatus'] = GraphQLParser::filterSelection($result, $selectedFields);
        }

        if (GraphQLParser::hasField($query, 'updateField')) {
            $result = $this->fieldResolver->updateField($query, $context);
            $selectedFields = GraphQLParser::selectedFields($query, 'updateField');

            $data['updateField'] = GraphQLParser::filterSelection($result, $selectedFields);
        }

        if (GraphQLParser::hasField($query, 'deleteField')) {
            $result = $this->fieldResolver->deleteField($query, $context);
            $selectedFields = GraphQLParser::selectedFields($query, 'deleteField');

            $data['deleteField'] = GraphQLParser::filterSelection($result, $selectedFields);
        }

        if (GraphQLParser::hasField($query, 'createBooking')) {
            $result = $this->bookingResolver->createBooking($query, $context);
            $selectedFields = GraphQLParser::selectedFields($query, 'createBooking');

            $data['createBooking'] = GraphQLParser::filterSelection($result, $selectedFields);
        }

        if (GraphQLParser::hasField($query, 'approveBooking')) {
            $result = $this->bookingResolver->approveBooking($query, $context);
            $selectedFields = GraphQLParser::selectedFields($query, 'approveBooking');

            $data['approveBooking'] = GraphQLParser::filterSelection($result, $selectedFields);
        }

        if (GraphQLParser::hasField($query, 'rejectBooking')) {
            $result = $this->bookingResolver->rejectBooking($query, $context);
            $selectedFields = GraphQLParser::selectedFields($query, 'rejectBooking');

            $data['rejectBooking'] = GraphQLParser::filterSelection($result, $selectedFields);
        }

        if (GraphQLParser::hasField($query, 'cancelBooking')) {
            $result = $this->bookingResolver->cancelBooking($query, $context);
            $selectedFields = GraphQLParser::selectedFields($query, 'cancelBooking');

            $data['cancelBooking'] = GraphQLParser::filterSelection($result, $selectedFields);
        }

        if (empty($data)) {
            $errors[] = [
                'message' => 'Query atau mutation tidak dikenali atau belum diimplementasikan.',
            ];
        }

        $response = [
            'data' => $data,
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        return $response;
    }

    public function healthOnly(): array
    {
        return $this->healthResolver->resolve();
    }
}

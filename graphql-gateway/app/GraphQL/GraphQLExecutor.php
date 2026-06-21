<?php

namespace App\GraphQL;

use App\GraphQL\Core\GraphQLParser;
use App\GraphQL\Resolvers\AuthMemberResolver;
use App\GraphQL\Resolvers\BookingResolver;
use App\GraphQL\Resolvers\DashboardResolver;
use App\GraphQL\Resolvers\FieldResolver;
use App\GraphQL\Resolvers\HealthResolver;
use App\GraphQL\Resolvers\NotificationResolver;

class GraphQLExecutor
{
    public function __construct(
        private readonly HealthResolver $healthResolver,
        private readonly FieldResolver $fieldResolver,
        private readonly BookingResolver $bookingResolver,
        private readonly AuthMemberResolver $authMemberResolver,
        private readonly NotificationResolver $notificationResolver,
        private readonly DashboardResolver $dashboardResolver
    ) {}

    public function execute(string $query, array $variables = [], array $context = []): array
    {
        $data = [];
        $errors = [];

        $this->resolveQuery($query, $context, $data);
        $this->resolveMutation($query, $context, $data);

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

    private function resolveQuery(string $query, array $context, array &$data): void
    {
        if (GraphQLParser::hasField($query, 'health')) {
            $result = $this->healthResolver->resolve();
            $selectedFields = GraphQLParser::selectedFields($query, 'health');

            $data['health'] = GraphQLParser::filterSelection($result, $selectedFields);
        }

        if (GraphQLParser::hasField($query, 'me')) {
            $result = $this->authMemberResolver->me($context);
            $selectedFields = GraphQLParser::selectedFields($query, 'me');

            $data['me'] = GraphQLParser::filterSelection($result, $selectedFields);
        }

        if (GraphQLParser::hasField($query, 'validateToken')) {
            $result = $this->authMemberResolver->validateToken($context);
            $selectedFields = GraphQLParser::selectedFields($query, 'validateToken');

            $data['validateToken'] = GraphQLParser::filterSelection($result, $selectedFields);
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
            $result = $this->fieldResolver->fieldSchedule($query);
            $selectedFields = GraphQLParser::selectedFields($query, 'fieldSchedule');

            $data['fieldSchedule'] = GraphQLParser::filterSelection($result, $selectedFields);
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

        if (GraphQLParser::hasField($query, 'memberByUserId')) {
            $result = $this->authMemberResolver->memberByUserId($query, $context);
            $selectedFields = GraphQLParser::selectedFields($query, 'memberByUserId');

            $data['memberByUserId'] = GraphQLParser::filterSelection($result, $selectedFields);
        }

        if (GraphQLParser::hasField($query, 'myProfile')) {
            $result = $this->authMemberResolver->myProfile($context);
            $selectedFields = GraphQLParser::selectedFields($query, 'myProfile');

            $data['myProfile'] = GraphQLParser::filterSelection($result, $selectedFields);
        }

        if (GraphQLParser::hasField($query, 'members')) {
            $result = $this->authMemberResolver->members($query, $context);
            $selectedFields = GraphQLParser::selectedFields($query, 'members');

            $data['members'] = GraphQLParser::filterSelection($result, $selectedFields);
        }

        if (GraphQLParser::hasField($query, 'member')) {
            $result = $this->authMemberResolver->member($query, $context);
            $selectedFields = GraphQLParser::selectedFields($query, 'member');

            $data['member'] = GraphQLParser::filterSelection($result, $selectedFields);
        }

        if (GraphQLParser::hasField($query, 'notificationLogs')) {
            $result = $this->notificationResolver->logs($query, $context);
            $selectedFields = GraphQLParser::selectedFields($query, 'notificationLogs');

            $data['notificationLogs'] = GraphQLParser::filterSelection($result, $selectedFields);
        }

        if (GraphQLParser::hasField($query, 'notificationLog')) {
            $result = $this->notificationResolver->log($query, $context);
            $selectedFields = GraphQLParser::selectedFields($query, 'notificationLog');

            $data['notificationLog'] = GraphQLParser::filterSelection($result, $selectedFields);
        }

        if (GraphQLParser::hasField($query, 'dashboardSummary')) {
            $result = $this->dashboardResolver->summary($context);
            $selectedFields = GraphQLParser::selectedFields($query, 'dashboardSummary');

            $data['dashboardSummary'] = GraphQLParser::filterSelection($result, $selectedFields);
        }
    }

    private function resolveMutation(string $query, array $context, array &$data): void
    {
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

        if (GraphQLParser::hasField($query, 'adminCreateMember')) {
            $result = $this->authMemberResolver->adminCreateMember($query, $context);
            $selectedFields = GraphQLParser::selectedFields($query, 'adminCreateMember');

            $data['adminCreateMember'] = GraphQLParser::filterSelection($result, $selectedFields);
        }

        if (GraphQLParser::hasField($query, 'deleteMemberAuthAccount')) {
            $result = $this->authMemberResolver->deleteMemberAuthAccount($query, $context);
            $selectedFields = GraphQLParser::selectedFields($query, 'deleteMemberAuthAccount');

            $data['deleteMemberAuthAccount'] = GraphQLParser::filterSelection($result, $selectedFields);
        }

        if (GraphQLParser::hasField($query, 'updateProfile')) {
            $result = $this->authMemberResolver->updateProfile($query, $context);
            $selectedFields = GraphQLParser::selectedFields($query, 'updateProfile');

            $data['updateProfile'] = GraphQLParser::filterSelection($result, $selectedFields);
        }

        if (GraphQLParser::hasField($query, 'updateMemberStatus')) {
            $result = $this->authMemberResolver->updateMemberStatus($query, $context);
            $selectedFields = GraphQLParser::selectedFields($query, 'updateMemberStatus');

            $data['updateMemberStatus'] = GraphQLParser::filterSelection($result, $selectedFields);
        }

        if (GraphQLParser::hasField($query, 'updateMember')) {
            $result = $this->authMemberResolver->updateMember($query, $context);
            $selectedFields = GraphQLParser::selectedFields($query, 'updateMember');

            $data['updateMember'] = GraphQLParser::filterSelection($result, $selectedFields);
        }

        if (GraphQLParser::hasField($query, 'deleteMember')) {
            $result = $this->authMemberResolver->deleteMember($query, $context);
            $selectedFields = GraphQLParser::selectedFields($query, 'deleteMember');

            $data['deleteMember'] = GraphQLParser::filterSelection($result, $selectedFields);
        }
    }
}

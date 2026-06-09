<?php

namespace App\GraphQL;

use App\GraphQL\Core\GraphQLParser;
use App\GraphQL\Resolvers\FieldResolver;
use App\GraphQL\Resolvers\HealthResolver;

class GraphQLExecutor
{
    public function __construct(
        private readonly HealthResolver $healthResolver,
        private readonly FieldResolver $fieldResolver
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

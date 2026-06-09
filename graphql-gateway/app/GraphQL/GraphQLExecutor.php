<?php

namespace App\GraphQL;

use App\GraphQL\Core\GraphQLParser;
use App\GraphQL\Resolvers\HealthResolver;

class GraphQLExecutor
{
    public function __construct(
        private readonly HealthResolver $healthResolver
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

        if (empty($data)) {
            $errors[] = [
                'message' => 'Query tidak dikenali atau belum diimplementasikan.',
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

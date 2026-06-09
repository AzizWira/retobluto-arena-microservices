<?php

namespace App\Http\Controllers\Api;

use App\GraphQL\GraphQLExecutor;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GraphQLController extends Controller
{
    public function execute(Request $request, GraphQLExecutor $executor): JsonResponse
    {
        $validated = $request->validate([
            'query' => ['required', 'string'],
            'variables' => ['nullable', 'array'],
        ]);

        $result = $executor->execute(
            query: $validated['query'],
            variables: $validated['variables'] ?? [],
            context: [
                'authorization' => $request->header('Authorization'),
            ]
        );

        return response()->json($result);
    }

    public function schema(): Response
    {
        $path = base_path('schema/schema.graphql');

        if (!file_exists($path)) {
            return response('Schema file not found.', 404);
        }

        return response(file_get_contents($path), 200)
            ->header('Content-Type', 'text/plain');
    }

    public function health(GraphQLExecutor $executor): JsonResponse
    {
        return response()->json([
            'success' => true,
            'service' => 'graphql-gateway',
            'data' => $executor->healthOnly(),
        ]);
    }
}

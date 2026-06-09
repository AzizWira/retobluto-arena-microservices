<?php

namespace App\GraphQL\Core;

class GraphQLParser
{
    public static function hasField(string $query, string $field): bool
    {
        return preg_match('/(^|[\s{])' . preg_quote($field, '/') . '(\s|\(|\{)/', $query) === 1;
    }

    public static function selectedFields(string $query, string $field): array
    {
        $pattern = '/(^|[\s{])' . preg_quote($field, '/') . '\s*(?:\([^)]*\))?\s*\{/s';

        if (!preg_match($pattern, $query, $matches, PREG_OFFSET_CAPTURE)) {
            return [];
        }

        $matchText = $matches[0][0];
        $matchOffset = $matches[0][1];
        $openBraceOffset = $matchOffset + strrpos($matchText, '{');

        $length = strlen($query);
        $depth = 0;
        $bodyStart = $openBraceOffset + 1;
        $bodyEnd = null;

        for ($i = $openBraceOffset; $i < $length; $i++) {
            $char = $query[$i];

            if ($char === '{') {
                $depth++;
            }

            if ($char === '}') {
                $depth--;

                if ($depth === 0) {
                    $bodyEnd = $i;
                    break;
                }
            }
        }

        if ($bodyEnd === null) {
            return [];
        }

        $body = substr($query, $bodyStart, $bodyEnd - $bodyStart);

        return self::parseTopLevelSelectedFields($body);
    }

    public static function filterSelection(mixed $data, array $selectedFields): mixed
    {
        if (empty($selectedFields)) {
            return $data;
        }

        if (!is_array($data)) {
            return $data;
        }

        $isList = array_keys($data) === range(0, count($data) - 1);

        if ($isList) {
            return array_map(
                fn($item) => self::filterSelection($item, $selectedFields),
                $data
            );
        }

        return array_intersect_key($data, array_flip($selectedFields));
    }

    private static function parseTopLevelSelectedFields(string $body): array
    {
        $fields = [];
        $length = strlen($body);
        $depth = 0;
        $current = '';

        for ($i = 0; $i < $length; $i++) {
            $char = $body[$i];

            if ($char === '{') {
                if ($current !== '') {
                    $fields[] = $current;
                    $current = '';
                }

                $depth++;
                continue;
            }

            if ($char === '}') {
                $depth--;
                continue;
            }

            if ($depth > 0) {
                continue;
            }

            if (preg_match('/[A-Za-z0-9_]/', $char)) {
                $current .= $char;
                continue;
            }

            if ($current !== '') {
                $fields[] = $current;
                $current = '';
            }
        }

        if ($current !== '') {
            $fields[] = $current;
        }

        return array_values(array_unique(array_filter($fields)));
    }
}

<?php

namespace App\GraphQL\Core;

class GraphQLParser
{
    public static function hasField(string $query, string $field): bool
    {
        return self::findTopLevelField($query, $field) !== null;
    }

    public static function args(string $query, string $field): array
    {
        $found = self::findTopLevelField($query, $field);

        if (!$found) {
            return [];
        }

        $i = self::skipWhitespace($query, $found['end']);

        if (!isset($query[$i]) || $query[$i] !== '(') {
            return [];
        }

        [$argsString] = self::readBalanced($query, $i, '(', ')');

        $argsString = trim($argsString);

        if ($argsString === '') {
            return [];
        }

        $args = [];

        preg_match_all(
            '/([A-Za-z_][A-Za-z0-9_]*)\s*:\s*("[^"]*"|true|false|null|-?\d+(?:\.\d+)?)/',
            $argsString,
            $matches,
            PREG_SET_ORDER
        );

        foreach ($matches as $match) {
            $args[$match[1]] = self::parseValue($match[2]);
        }

        return $args;
    }

    public static function selectedFields(string $query, string $field): array
    {
        $found = self::findTopLevelField($query, $field);

        if (!$found) {
            return [];
        }

        $i = self::skipWhitespace($query, $found['end']);

        if (isset($query[$i]) && $query[$i] === '(') {
            [, $i] = self::readBalanced($query, $i, '(', ')');
            $i = self::skipWhitespace($query, $i);
        }

        if (!isset($query[$i]) || $query[$i] !== '{') {
            return [];
        }

        [$body] = self::readBalanced($query, $i, '{', '}');

        return self::parseSelectedFieldsTree($body);
    }

    public static function filterSelection(mixed $data, array $selectedFields): mixed
    {
        if (empty($selectedFields)) {
            return $data;
        }

        if (!is_array($data)) {
            return $data;
        }

        if (array_is_list($data)) {
            return array_map(
                fn($item) => self::filterSelection($item, $selectedFields),
                $data
            );
        }

        $filtered = [];

        foreach ($selectedFields as $field => $children) {
            if (is_int($field)) {
                $field = $children;
                $children = true;
            }

            if (!is_string($field) || !array_key_exists($field, $data)) {
                continue;
            }

            if (is_array($children)) {
                $filtered[$field] = self::filterSelection($data[$field], $children);
            } else {
                $filtered[$field] = $data[$field];
            }
        }

        return $filtered;
    }

    private static function findTopLevelField(string $query, string $field): ?array
    {
        $bounds = self::operationBounds($query);

        if (!$bounds) {
            return null;
        }

        [$start, $end] = $bounds;

        $i = $start + 1;
        $depth = 0;
        $paren = 0;
        $inString = false;
        $escape = false;
        $length = strlen($query);

        while ($i < $end && $i < $length) {
            $char = $query[$i];

            if ($inString) {
                if ($escape) {
                    $escape = false;
                    $i++;
                    continue;
                }

                if ($char === '\\') {
                    $escape = true;
                    $i++;
                    continue;
                }

                if ($char === '"') {
                    $inString = false;
                }

                $i++;
                continue;
            }

            if ($char === '"') {
                $inString = true;
                $i++;
                continue;
            }

            if ($paren > 0) {
                if ($char === '(') {
                    $paren++;
                } elseif ($char === ')') {
                    $paren--;
                }

                $i++;
                continue;
            }

            if ($char === '(') {
                $paren++;
                $i++;
                continue;
            }

            if ($char === '{') {
                $depth++;
                $i++;
                continue;
            }

            if ($char === '}') {
                if ($depth > 0) {
                    $depth--;
                }

                $i++;
                continue;
            }

            if ($depth === 0 && preg_match('/[A-Za-z_]/', $char)) {
                $tokenStart = $i;
                $token = '';

                while (
                    $i < $end
                    && isset($query[$i])
                    && preg_match('/[A-Za-z0-9_]/', $query[$i])
                ) {
                    $token .= $query[$i];
                    $i++;
                }

                if ($token === $field) {
                    return [
                        'start' => $tokenStart,
                        'end' => $i,
                    ];
                }

                continue;
            }

            $i++;
        }

        return null;
    }

    private static function operationBounds(string $query): ?array
    {
        $start = strpos($query, '{');

        if ($start === false) {
            return null;
        }

        $depth = 0;
        $inString = false;
        $escape = false;
        $length = strlen($query);

        for ($i = $start; $i < $length; $i++) {
            $char = $query[$i];

            if ($inString) {
                if ($escape) {
                    $escape = false;
                    continue;
                }

                if ($char === '\\') {
                    $escape = true;
                    continue;
                }

                if ($char === '"') {
                    $inString = false;
                }

                continue;
            }

            if ($char === '"') {
                $inString = true;
                continue;
            }

            if ($char === '{') {
                $depth++;
            }

            if ($char === '}') {
                $depth--;

                if ($depth === 0) {
                    return [$start, $i];
                }
            }
        }

        return null;
    }

    private static function readBalanced(string $query, int $start, string $open, string $close): array
    {
        $depth = 0;
        $inString = false;
        $escape = false;
        $bodyStart = $start + 1;
        $length = strlen($query);

        for ($i = $start; $i < $length; $i++) {
            $char = $query[$i];

            if ($inString) {
                if ($escape) {
                    $escape = false;
                    continue;
                }

                if ($char === '\\') {
                    $escape = true;
                    continue;
                }

                if ($char === '"') {
                    $inString = false;
                }

                continue;
            }

            if ($char === '"') {
                $inString = true;
                continue;
            }

            if ($char === $open) {
                $depth++;
            }

            if ($char === $close) {
                $depth--;

                if ($depth === 0) {
                    return [
                        substr($query, $bodyStart, $i - $bodyStart),
                        $i + 1,
                    ];
                }
            }
        }

        return ['', $start];
    }

    private static function skipWhitespace(string $query, int $start): int
    {
        $length = strlen($query);

        while ($start < $length && ctype_space($query[$start])) {
            $start++;
        }

        return $start;
    }

    private static function parseSelectedFieldsTree(string $body): array
    {
        $fields = [];
        $length = strlen($body);
        $i = 0;

        while ($i < $length) {
            $i = self::skipWhitespace($body, $i);

            if ($i >= $length) {
                break;
            }

            if (!isset($body[$i]) || !preg_match('/[A-Za-z_]/', $body[$i])) {
                $i++;
                continue;
            }

            $name = self::readName($body, $i);

            $i = self::skipWhitespace($body, $i);

            if (isset($body[$i]) && $body[$i] === ':') {
                $alias = $name;

                $i++;
                $i = self::skipWhitespace($body, $i);

                if (isset($body[$i]) && preg_match('/[A-Za-z_]/', $body[$i])) {
                    self::readName($body, $i);
                    $name = $alias;
                }

                $i = self::skipWhitespace($body, $i);
            }

            if (isset($body[$i]) && $body[$i] === '(') {
                [, $i] = self::readBalanced($body, $i, '(', ')');
                $i = self::skipWhitespace($body, $i);
            }

            if (isset($body[$i]) && $body[$i] === '{') {
                [$childBody, $i] = self::readBalanced($body, $i, '{', '}');
                $childFields = self::parseSelectedFieldsTree($childBody);

                if (isset($fields[$name]) && is_array($fields[$name])) {
                    $fields[$name] = array_replace_recursive($fields[$name], $childFields);
                } else {
                    $fields[$name] = $childFields;
                }

                continue;
            }

            $fields[$name] = true;
        }

        return $fields;
    }

    private static function readName(string $query, int &$i): string
    {
        $name = '';
        $length = strlen($query);

        while (
            $i < $length
            && isset($query[$i])
            && preg_match('/[A-Za-z0-9_]/', $query[$i])
        ) {
            $name .= $query[$i];
            $i++;
        }

        return $name;
    }

    private static function parseValue(string $value): mixed
    {
        $value = trim($value);

        if (str_starts_with($value, '"') && str_ends_with($value, '"')) {
            return stripcslashes(substr($value, 1, -1));
        }

        if ($value === 'true') {
            return true;
        }

        if ($value === 'false') {
            return false;
        }

        if ($value === 'null') {
            return null;
        }

        if (is_numeric($value)) {
            return str_contains($value, '.') ? (float) $value : (int) $value;
        }

        return $value;
    }
}

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

    private static function parseTopLevelSelectedFields(string $body): array
    {
        $fields = [];
        $length = strlen($body);
        $depth = 0;
        $paren = 0;
        $inString = false;
        $escape = false;
        $current = '';

        for ($i = 0; $i < $length; $i++) {
            $char = $body[$i];

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

            if ($char === '(') {
                $paren++;
                continue;
            }

            if ($char === ')') {
                if ($paren > 0) {
                    $paren--;
                }

                continue;
            }

            if ($paren > 0) {
                continue;
            }

            if ($char === '{') {
                if ($current !== '') {
                    $fields[] = $current;
                    $current = '';
                }

                $depth++;
                continue;
            }

            if ($char === '}') {
                if ($depth > 0) {
                    $depth--;
                }

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

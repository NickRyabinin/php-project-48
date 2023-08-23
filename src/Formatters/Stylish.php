<?php

namespace Differ\Formatters\Stylish;

function stylishFormat(array $diff): string
{
    $formattedDiff = makeStringsFromDiff($diff);
    $result = implode("\n", $formattedDiff);

    return "{\n{$result}\n}";
}

function makeStringsFromDiff(array $diff, int $level = 0): array
{
    $spaces = getSpaces($level);
    $nextLevel = $level + 1;

    $callback = function ($node) use ($spaces, $nextLevel) {
        list('status' => $status, 'key' => $key, 'value1' => $value1, 'value2' => $value2) = $node;

        switch ($status) {
            case 'nested':
                $nested = makeStringsFromDiff($value1, $nextLevel);
                $stringifiedNest = implode("\n", $nested);
                return "{$spaces}    {$key}: {\n{$stringifiedNest}\n{$spaces}    }";
            case 'same':
                $stringifiedValue1 = stringifyValue($value1, $nextLevel);
                return "{$spaces}    {$key}: {$stringifiedValue1}";
            case 'added':
                $stringifiedValue1 = stringifyValue($value1, $nextLevel);
                return "{$spaces}  + {$key}: {$stringifiedValue1}";
            case 'removed':
                $stringifiedValue1 = stringifyValue($value1, $nextLevel);
                return "{$spaces}  - {$key}: {$stringifiedValue1}";
            case 'updated':
                $stringifiedValue1 = stringifyValue($value1, $nextLevel);
                $stringifiedValue2 = stringifyValue($value2, $nextLevel);
                return "{$spaces}  - {$key}: {$stringifiedValue1}\n{$spaces}  + {$key}: {$stringifiedValue2}";
        }
    };
    return array_map($callback, $diff);
}

function getSpaces(int $level): string
{
    return str_repeat('    ', $level);
}

function stringifyValue(mixed $value, int $level): mixed
{
    if (is_null($value)) {
        return 'null';
    }
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }
    if (is_array($value)) {
        $result = convertArrayToString($value, $level);
        $spaces = getSpaces($level);
        return "{{$result}\n{$spaces}}";
    }
    return "{$value}";
}

function convertArrayToString(array $value, int $level): string
{
    $keys = array_keys($value);
    $result = [];
    $nextLevel = $level + 1;

    $callback = function ($key) use ($value, $nextLevel) {
        $newValue = stringifyValue($value[$key], $nextLevel);
        $spaces = getSpaces($nextLevel);

        return "\n{$spaces}{$key}: {$newValue}";
    };

    return implode('', array_map($callback, $keys));
}

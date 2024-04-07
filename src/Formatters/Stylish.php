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

    $formatNode = function ($node) use ($spaces, $nextLevel) {
        ['status' => $status, 'key' => $key, 'value1' => $value1, 'value2' => $value2] = $node;
        return match ($status) {
            'nested' => formatNested($key, $value1, $spaces, $nextLevel),
            'same' => formatSame($key, $value1, $spaces, $nextLevel),
            'added' => formatAdded($key, $value1, $spaces, $nextLevel),
            'removed' => formatRemoved($key, $value1, $spaces, $nextLevel),
            'updated' => formatUpdated($node, $spaces, $nextLevel)
        };
    };

    return array_map($formatNode, $diff);
}

function formatNested(mixed $key, mixed $value, string $spaces, int $nextLevel): string
{
    $nested = makeStringsFromDiff($value, $nextLevel);
    $stringifiedNest = implode("\n", $nested);
    return "{$spaces}    {$key}: {\n{$stringifiedNest}\n{$spaces}    }";
}

function formatSame(mixed $key, mixed $value, string $spaces, int $nextLevel): string
{
    $stringifiedValue = stringifyValue($value, $nextLevel);
    return "{$spaces}    {$key}: {$stringifiedValue}";
}

function formatAdded(mixed $key, mixed $value, string $spaces, int $nextLevel): string
{
    $stringifiedValue = stringifyValue($value, $nextLevel);
    return "{$spaces}  + {$key}: {$stringifiedValue}";
}

function formatRemoved(mixed $key, mixed $value, string $spaces, int $nextLevel): string
{
    $stringifiedValue = stringifyValue($value, $nextLevel);
    return "{$spaces}  - {$key}: {$stringifiedValue}";
}

function formatUpdated(array $node, string $spaces, int $nextLevel): string
{
    extract($node);
    $stringifiedValue1 = stringifyValue($value1, $nextLevel);
    $stringifiedValue2 = stringifyValue($value2, $nextLevel);
    return "{$spaces}  - {$key}: {$stringifiedValue1}\n{$spaces}  + {$key}: {$stringifiedValue2}";
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

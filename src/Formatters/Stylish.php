<?php

namespace Differ\Formatters\Stylish;

function stylishFormat(array $diff): string
{
    $formattedDiff = makeStringsFromDiff($diff);
    $result = implode("\n", $formattedDiff);

    return "{\n{$result}\n}";
}

function makeStringsFromDiff(array $diff): array
{
    $stringifiedDiff = [];

    foreach ($diff as $node) {
        $status = $node['status'];
        $key = $node['key'];
        $value1 = $node['value1'];
        $value2 = $node['value2'];

        switch ($status) {
            case 'nested':
                $nested = makeStringsFromDiff($value1);
                $stringifiedNest = implode("\n", $nested);
                $stringifiedDiff[] = "    {$key}: {\n{$stringifiedNest}\n    }";
                break;
            case 'same':
                $stringifiedValue1 = stringifyValue($value1);
                $stringifiedDiff[] = "    {$key}: {$stringifiedValue1}";
                break;
            case 'added':
                $stringifiedValue1 = stringifyValue($value1);
                $stringifiedDiff[] = "  + {$key}: {$stringifiedValue1}";
                break;
            case 'removed':
                $stringifiedValue1 = stringifyValue($value1);
                $stringifiedDiff[] = "  - {$key}: {$stringifiedValue1}";
                break;
            case 'updated':
                $stringifiedValue1 = stringifyValue($value1);
                $stringifiedValue2 = stringifyValue($value2);
                $stringifiedDiff[] = "  - {$key}: {$stringifiedValue1}\n  + {$key}: {$stringifiedValue2}";
        }
    }
    return $stringifiedDiff;
}

function stringifyValue(mixed $value): mixed
{
    if (is_null($value)) {
        return 'null';
    }
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }
    if (is_array($value)) {
        $result = convertArrayToString($value);
        return "{$result}\n";
    }
    return "{$value}";
}

function convertArrayToString(array $value): string
{
    $keys = array_keys($value);
    $result = [];

    $callback = function ($key) use ($value) {
        $newValue = stringifyValue($value[$key]);
        return "\n{$key}: {$newValue}";
    };

    $result = array_map($callback, $keys);

    return implode('', $result);
}

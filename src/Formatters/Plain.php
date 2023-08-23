<?php

namespace Differ\Formatters\Plain;

use function Functional\flatten;

function plainFormat(array $diff): string
{
    $formattedDiff = makeStringsFromDiff($diff);
    $result = implode("\n", $formattedDiff);

    return "{$result}\n";
}

function makeStringsFromDiff(array $diff, string $path = ''): array
{
    $stringifiedDiff = [];

    foreach ($diff as $node) {
        $status = $node['status'];
        $key = $node['key'];
        $value1 = $node['value1'];
        $value2 = $node['value2'];
        $fullPath = "{$path}{$key}";

        switch ($status) {
            case 'nested':
                $fullPath = "{$path}{$key}.";
                $nested = makeStringsFromDiff($value1, $fullPath);
                $stringifiedDiff[] = $nested;
                break;
            case 'same':
                break;
            case 'added':
                $stringifiedValue1 = stringifyValue($value1);
                $stringifiedDiff[] = "Property '{$fullPath}' was added with value: {$stringifiedValue1}";
                break;
            case 'removed':
                $stringifiedDiff[] = "Property '{$fullPath}' was removed";
                break;
            case 'updated':
                $stringifiedValue1 = stringifyValue($value1);
                $stringifiedValue2 = stringifyValue($value2);
                $stringifiedDiff[] =
                    "Property '{$fullPath}' was updated. From {$stringifiedValue1} to {$stringifiedValue2}";
        }
    }
    return flatten($stringifiedDiff);
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
        return '[complex value]';
    }
    if (is_numeric($value)) {
        return $value;
    }
    return "'{$value}'";
}

<?php

namespace Differ\Formatters\Plain;

function plainFormat(array $diff): string
{
    $formattedDiff = makeStringsFromDiff($diff);
    $result = implode("\n", $formattedDiff);

    return "\n{$result}\n";
}

function makeStringsFromDiff(array $diff): array
{
    $stringifiedDiff = [];
    // $nested = [];
    $fullPath = '';

    foreach ($diff as $node) {
        $status = $node['status'];
        $key = $node['key'];
        $value1 = $node['value1'];
        $value2 = $node['value2'];

        switch ($status) {
            case 'nested':
                $fullPath .= $key;
                $nested = makeStringsFromDiff($value1);
                $stringifiedDiff[] = $nested;
                break;
            case 'same':
                break;
            case 'added':
                $fullPath .= $key;
                $stringifiedValue1 = stringifyValue($value1);
                $stringifiedDiff[] = "Property '{$fullPath}' was added with value: {$stringifiedValue1}";
                break;
            case 'removed':
                $fullPath .= $key;
                $stringifiedDiff[] = "Property '{$fullPath}' was removed";
                break;
            case 'updated':
                $fullPath .= $key;
                $stringifiedValue1 = stringifyValue($value1);
                $stringifiedValue2 = stringifyValue($value2);
                $stringifiedDiff[] =
                    "Property '{$fullPath}' was updated. From {$stringifiedValue1} to {$stringifiedValue2}";
        }
    }
    echo "\nStringified Diff:\n";
    var_export($stringifiedDiff);
    echo "\n";
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
        return '[complex value]';
    }
    return "'{$value}'";
}

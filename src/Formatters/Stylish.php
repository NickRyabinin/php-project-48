<?php

namespace Differ\Formatters\Stylish;

function stylishFormat(array $diff): string
{
    $formattedDiff = makeStringFromDiff($diff);
    return "{\n{$formattedDiff}\n}";
}

function makeStringFromDiff(array $diff): string
{
    $stringifiedDiff = [];

    foreach ($diff as $node) {
        echo "\nNode:\n";
        var_export($node);
        echo "\n";
        $status = $node['status'];
        $key = $node['key'];
        $value1 = $node['value1'];
        $value2 = $node['value2'];
        $stringifiedValue1 = (is_array($value1)) ? makeStringFromDiff($value1) : stringifyValue($value1);

        switch ($status) {
            case 'nested':
            case 'same':
                $stringifiedDiff[] = "    {$key}: {$stringifiedValue1}";
                break;
            case 'added':
                $stringifiedDiff[] = "  + {$key}: {$stringifiedValue1}";
                break;
            case 'removed':
                $stringifiedDiff[] = "  - {$key}: {$stringifiedValue1}";
                break;
            case 'updated':
                $stringifiedValue2 = (is_array($value2)) ? makeStringFromDiff($value2) : stringifyValue($value2);
                $stringifiedDiff[] = "  - {$key}: {$stringifiedValue1}\n  + {$key}: {$stringifiedValue2}";
        }
    }
    return implode("\n", $stringifiedDiff);
}

function stringifyValue(mixed $value): mixed
{
    if (is_null($value)) {
        return 'null';
    }
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }
    return $value;
}

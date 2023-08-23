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
    $stringifiedDiff = [];
    $spaces = getSpaces($level);
    $nextLevel = $level + 1;

    foreach ($diff as $node) {
        $status = $node['status'];
        $key = $node['key'];
        $value1 = $node['value1'];
        $value2 = $node['value2'];

        switch ($status) {
            case 'nested':
                $nested = makeStringsFromDiff($value1, $nextLevel);
                $stringifiedNest = implode("\n", $nested);
                $stringifiedDiff[] = "{$spaces}    {$key}: {\n{$stringifiedNest}\n{$spaces}    }";
                break;
            case 'same':
                $stringifiedValue1 = stringifyValue($value1, $nextLevel);
                $stringifiedDiff[] = "{$spaces}    {$key}: {$stringifiedValue1}";
                break;
            case 'added':
                $stringifiedValue1 = stringifyValue($value1, $nextLevel);
                $stringifiedDiff[] = "{$spaces}  + {$key}: {$stringifiedValue1}";
                break;
            case 'removed':
                $stringifiedValue1 = stringifyValue($value1, $nextLevel);
                $stringifiedDiff[] = "{$spaces}  - {$key}: {$stringifiedValue1}";
                break;
            case 'updated':
                $stringifiedValue1 = stringifyValue($value1, $nextLevel);
                $stringifiedValue2 = stringifyValue($value2, $nextLevel);
                $stringifiedDiff[] =
                    "{$spaces}  - {$key}: {$stringifiedValue1}\n{$spaces}  + {$key}: {$stringifiedValue2}";
        }
    }
    return $stringifiedDiff;
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

    $result = array_map($callback, $keys);

    return implode('', $result);
}

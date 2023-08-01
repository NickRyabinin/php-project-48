<?php

namespace Differ\Formatters\Stylish;

function stylishFormat(array $diff): string
{
    // var_export($diff);
    // $result = implode("\n", $diff);
    // return "{\n{$result}\n}";
    return var_export($diff);
}

function makeStringFromDiff(array $diff, int $level = 0): string
{
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

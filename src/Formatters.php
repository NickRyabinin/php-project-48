<?php

namespace Differ\Formatters;

function makeFormat($diff, $formatName)
{
    var_export($diff);
    $result = implode("\n", $diff);
    return "{\n{$result}\n}";
}

function stringifyValue($value)
{
    if (is_null($value)) {
        return 'null';
    }
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }
    return $value;
}

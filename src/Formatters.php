<?php

namespace Differ\Formatters;

function makeFormat($diff, $formatName)
{
    $result = implode("\n", $diff);
    return "{\n{$result}\n}";
}

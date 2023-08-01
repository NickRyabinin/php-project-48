<?php

namespace Differ\Formatters;

use function Differ\Formatters\Stylish\stylishFormat;

function makeFormat($diff, $formatName)
{
    return match ($formatName) {
        'stylish' => stylishFormat($diff),
        default => exit("Unknown format '{$formatName}'!\n")
    };
}

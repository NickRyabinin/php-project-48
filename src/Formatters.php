<?php

namespace Differ\Formatters;

use function Differ\Formatters\Stylish\stylishFormat;
use function Differ\Formatters\Plain\plainFormat;

function makeFormat(array $diff, string $formatName): string
{
    return match ($formatName) {
        'stylish' => stylishFormat($diff),
        'plain' => plainFormat($diff),
        default => exit("Unknown format '{$formatName}'!\n")
    };
}

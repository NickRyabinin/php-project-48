<?php

namespace Differ\Formatters;

use function Differ\Formatters\Stylish\stylishFormat;
use function Differ\Formatters\Plain\plainFormat;
use function Differ\Formatters\Json\jsonFormat;

function makeFormat(array $diff, string $formatName): string
{
    return match ($formatName) {
        'stylish' => stylishFormat($diff),
        'plain' => plainFormat($diff),
        'json' => jsonFormat($diff),
        default => exit("Unknown format '{$formatName}'!\n")
    };
}

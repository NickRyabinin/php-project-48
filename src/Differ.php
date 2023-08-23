<?php

namespace Differ\Differ;

use function Differ\Parsers\parse;
use function Differ\Formatters\makeFormat;
use function Functional\sort;

function genDiff(string $pathToFile1, string $pathToFile2, string $formatName = 'stylish'): string
{
    $parsedContentOfFile1 = parse($pathToFile1);
    $parsedContentOfFile2 = parse($pathToFile2);

    $diff = makeDiff($parsedContentOfFile1, $parsedContentOfFile2);
    $result = makeFormat($diff, $formatName);

    return $result;
}

function makeDiff(array $parsedContentOfFile1, array $parsedContentOfFile2): array
{
    $allUniqueKeys = getSortedUniqueKeys($parsedContentOfFile1, $parsedContentOfFile2);

    $callback = function ($uniqueKey) use ($parsedContentOfFile1, $parsedContentOfFile2) {
        $value1 = $parsedContentOfFile1[$uniqueKey] ?? null;
        $value2 = $parsedContentOfFile2[$uniqueKey] ?? null;

        if (is_array($value1) && is_array($value2)) {
            return ['status' => 'nested',
                'key' => $uniqueKey,
                'value1' => makeDiff($value1, $value2),
                'value2' => null];
        }

        if (!array_key_exists($uniqueKey, $parsedContentOfFile1)) {
            return ['status' => 'added',
                'key' => $uniqueKey,
                'value1' => $value2,
                'value2' => null];
        }

        if (!array_key_exists($uniqueKey, $parsedContentOfFile2)) {
            return ['status' => 'removed',
                'key' => $uniqueKey,
                'value1' => $value1,
                'value2' => null];
        }

        if ($value1 === $value2) {
            return ['status' => 'same',
            'key' => $uniqueKey,
            'value1' => $value1,
            'value2' => null];
        }

        if ($value1 !== $value2) {
            return ['status' => 'updated',
            'key' => $uniqueKey,
            'value1' => $value1,
            'value2' => $value2];
        }
    };
    return array_map($callback, $allUniqueKeys);
}

function getSortedUniqueKeys(array $parsedContentOfFile1, array $parsedContentOfFile2): array
{
    $keysOfFile1 = array_keys($parsedContentOfFile1);
    $keysOfFile2 = array_keys($parsedContentOfFile2);
    $keysOfBothFiles = array_merge($keysOfFile1, $keysOfFile2);
    $allUniqueKeys = array_unique($keysOfBothFiles);
    return sort($allUniqueKeys, fn ($left, $right) => strcmp($left, $right));
}

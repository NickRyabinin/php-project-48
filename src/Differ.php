<?php

namespace Differ\Differ;

use function Differ\Parsers\parse;
use function Differ\Formatters\makeFormat;

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
    $differ = [];
    $allUniqueKeys = getSortedUniqueKeys($parsedContentOfFile1, $parsedContentOfFile2);

    foreach ($allUniqueKeys as $uniqueKey) {
        $value1 = $parsedContentOfFile1[$uniqueKey] ?? null;
        $value2 = $parsedContentOfFile2[$uniqueKey] ?? null;

        if (is_array($value1) && is_array($value2)) {
            $differ[] = ['status' => 'nested',
                'key' => $uniqueKey,
                'value1' => makeDiff($value1, $value2),
                'value2' => null];
                continue;
        }

        if (!array_key_exists($uniqueKey, $parsedContentOfFile1)) {
            $differ[] = ['status' => 'added',
                'key' => $uniqueKey,
                'value1' => $value2,
                'value2' => null];
                continue;
        }

        if (!array_key_exists($uniqueKey, $parsedContentOfFile2)) {
            $differ[] = ['status' => 'removed',
                'key' => $uniqueKey,
                'value1' => $value1,
                'value2' => null];
                continue;
        }

        if ($value1 === $value2) {
            $differ[] = ['status' => 'same',
            'key' => $uniqueKey,
            'value1' => $value1,
            'value2' => null];
            continue;
        }

        if ($value1 !== $value2) {
            $differ[] = ['status' => 'updated',
            'key' => $uniqueKey,
            'value1' => $value1,
            'value2' => $value2];
            continue;
        }
    }
    return $differ;
}

function getSortedUniqueKeys(array $parsedContentOfFile1, array $parsedContentOfFile2): array
{
    $keysOfFile1 = array_keys($parsedContentOfFile1);
    $keysOfFile2 = array_keys($parsedContentOfFile2);
    $keysOfBothFiles = array_merge($keysOfFile1, $keysOfFile2);
    $allUniqueKeys = array_unique($keysOfBothFiles);
    sort($allUniqueKeys);
    return $allUniqueKeys;
}

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

function makeDiff($parsedContentOfFile1, $parsedContentOfFile2)
{
    $allUniqueKeys = array_unique(array_merge(array_keys($parsedContentOfFile1), array_keys($parsedContentOfFile2)));
    sort($allUniqueKeys);

    $flag = '';
    $differ = [];

    foreach ($allUniqueKeys as $uniqueKey) {
        if (
            array_key_exists($uniqueKey, $parsedContentOfFile1)
            && array_key_exists($uniqueKey, $parsedContentOfFile2)
        ) {
            if ($parsedContentOfFile1[$uniqueKey] === $parsedContentOfFile2[$uniqueKey]) {
                $flag = ' ';
                $value = stringifyBooleanValue($parsedContentOfFile1[$uniqueKey]);
                $differ[] = "  {$flag} {$uniqueKey}: {$value}";
            } else {
                $flag = '-';
                $value = stringifyBooleanValue($parsedContentOfFile1[$uniqueKey]);
                $differ[] = "  {$flag} {$uniqueKey}: {$value}";
                $flag = '+';
                $value = stringifyBooleanValue($parsedContentOfFile2[$uniqueKey]);
                $differ[] = "  {$flag} {$uniqueKey}: {$value}";
            }
        } elseif (
            array_key_exists($uniqueKey, $parsedContentOfFile1)
            && !array_key_exists($uniqueKey, $parsedContentOfFile2)
        ) {
            $flag = '-';
            $value = stringifyBooleanValue($parsedContentOfFile1[$uniqueKey]);
            $differ[] = "  {$flag} {$uniqueKey}: {$value}";
        } elseif (
            !array_key_exists($uniqueKey, $parsedContentOfFile1)
            && array_key_exists($uniqueKey, $parsedContentOfFile2)
        ) {
            $flag = '+';
            $value = stringifyBooleanValue($parsedContentOfFile2[$uniqueKey]);
            $differ[] = "  {$flag} {$uniqueKey}: {$value}";
        }
    }
    return $differ;
}

function stringifyBooleanValue($value)
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }
    return $value;
}

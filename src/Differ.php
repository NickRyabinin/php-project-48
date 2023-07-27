<?php

namespace Differ\Differ;

function genDiff(string $pathToFile1, string $pathToFile2, string $formatName): string
{
    $contentOfFile1 = file_get_contents($pathToFile1);
    $contentOfFile2 = file_get_contents($pathToFile2);

    $parsedContentOfFile1 = json_decode($contentOfFile1, true);
    $parsedContentOfFile2 = json_decode($contentOfFile2, true);

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
    $result = implode("\n", $differ);
    return "{\n{$result}\n}";
}

function stringifyBooleanValue($value)
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }
    return $value;
}

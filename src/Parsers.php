<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function getFileContent(string $pathToFile)
{
    $contentOfFile = @file_get_contents($pathToFile);
    if ($contentOfFile !== false) {
        return $contentOfFile;
    }
    throw new \Exception("File not found", 1);
}

function parse(string $pathToFile)
{
    $contentOfFile = getFileContent($pathToFile);
    $extensionOfFile = pathinfo($pathToFile, PATHINFO_EXTENSION);
    switch ($extensionOfFile) {
        case 'json':
            $parsedContentOfFile = json_decode($contentOfFile, true);
            break;
        case 'yml':
        case 'yaml':
            $parsedContentOfFile = Yaml::parse($contentOfFile);
            break;
        default:
            throw new \Exception("Unsupported format of incoming file!", 1);
    }

    return $parsedContentOfFile;
}

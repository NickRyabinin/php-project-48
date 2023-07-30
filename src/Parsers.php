<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function getFileContent(string $pathToFile)
{
    $contentOfFile = @file_get_contents($pathToFile);
    return ($contentOfFile) ? $contentOfFile : exit("\nFile '{$pathToFile}' not found!\n");
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
            exit("\nUnsupported format '{$extensionOfFile}' of incoming file!\n");
    }

    echo "\nParsed Content:\n";
    var_export($parsedContentOfFile);
    echo "\n";

    return $parsedContentOfFile;
}

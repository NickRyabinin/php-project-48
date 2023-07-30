<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function getFileContent(string $pathToFile)
{
    $contentOfFile = file_get_contents($pathToFile);
    return ($contentOfFile) ? $contentOfFile : exit("File {$pathToFile} not found");
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
            exit("Unsupported format {$extensionOfFile} of incoming file!");
    }
    return $parsedContentOfFile;
}

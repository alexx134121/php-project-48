<?php

namespace Differ;

const  YAML_EXTENSION = ['yaml','yml'];
const  JSON_EXTENSION = 'json';

function ymlParser(string $content): array
{
    return \Symfony\Component\Yaml\Yaml::parse($content);
}

function jsonParser(string $content): array
{
    return json_decode($content, true, flags: JSON_THROW_ON_ERROR);
}

function getDataFromFile(string $pathToFile): string
{
    $path = realpath($pathToFile);
    if (!$path) {
        throw new \Exception("Не найден путь к файлу $pathToFile");
    }
    $content = file_get_contents($path);
    return $content;
}

function parser(string $path): array
{
    $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    if (in_array($extension, YAML_EXTENSION)) {
        return ymlParser(getDataFromFile($path));
    }
    if ($extension == JSON_EXTENSION) {
        return jsonParser(getDataFromFile($path));
    }
    throw new \Exception(' Формат не поддерживается');
}

<?php

namespace Differ\FileReader;

function getDataFromFile(string $pathToFile): string
{
    $path = realpath($pathToFile);

    if ($path === false) {
        throw new \Exception("Не найден путь к файлу $pathToFile");
    }
    $content = file_get_contents($path);
    if ($content === false) {
        throw new \Exception("Ошибка чтения файла $pathToFile");
    }

    return $content;
}

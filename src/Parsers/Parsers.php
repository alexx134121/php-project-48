<?php

namespace Differ\Parsers\Parsers;

use Exception;

use function Differ\FileReader\getDataFromFile;
use function Differ\Parsers\Yaml\parse as getFromYaml;
use function Differ\Parsers\Json\parse as getFromJson;

function parserData(string $path): array
{
    $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    return match ($extension) {
        'yaml', 'yml' => getFromYaml(getDataFromFile($path)),
        'json' => getFromJson(getDataFromFile($path)),
        default => throw new Exception(' Формат не поддерживается'),
    };
}

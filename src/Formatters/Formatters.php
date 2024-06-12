<?php

namespace Differ\Formatters\Formatters;

use Exception;

use function Differ\Formatters\Json\format as jsonFormat;
use function Differ\Formatters\Plain\format as plainFormat;
use function Differ\Formatters\Stylish\format as stylishFormat;

const STYLISH = 'stylish';
const PLAIN = 'plain';
const JSON = 'json';

function format(array $data, string $type = STYLISH): string
{
    return match ($type) {
        STYLISH => stylishFormat($data),
        PLAIN => plainFormat($data),
        JSON => jsonFormat($data),
        default => throw new Exception("Формат $type недоступен")
    };
}

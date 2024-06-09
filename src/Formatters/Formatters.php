<?php

namespace Differ\Formatters\Formatters;

use function Differ\Formatters\Json\json;
use function Differ\Formatters\Plain\plain;
use function Differ\Formatters\Stylish\stylish;

const STYLISH = 'stylish';
const PLAIN = 'plain';
const JSON = 'json';

function format(array $data, string $type = STYLISH): string
{
    return match ($type) {
        STYLISH => stylish($data),
        PLAIN => plain($data),
        JSON => json($data),
        default => throw new \Exception("Формат $type недоступен")
    };
}

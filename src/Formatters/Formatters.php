<?php

namespace Diff\Formatters;

use function Diff\Formatters\stylish;

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

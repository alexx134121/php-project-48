<?php

namespace Differ\Formatters\Json;

function json(array $data): false | string
{
    return json_encode($data, JSON_PRETTY_PRINT);
}

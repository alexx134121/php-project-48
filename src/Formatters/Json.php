<?php

namespace Differ\Formatters;

function json(array $data): false | string
{
    return json_encode($data, JSON_PRETTY_PRINT);
}

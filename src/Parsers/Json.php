<?php

namespace Differ\Parsers\Json;

function parse(string $content): array
{
    return json_decode($content, true, flags: JSON_THROW_ON_ERROR);
}

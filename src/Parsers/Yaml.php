<?php

namespace Differ\Parsers\Yaml;

use Symfony\Component\Yaml\Yaml;

function parse(string $content): array
{
    return Yaml::parse($content);
}

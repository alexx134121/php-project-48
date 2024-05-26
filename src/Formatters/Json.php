<?php

namespace Diff\Formatters;

use function Diff\getChild;
use function Diff\getKey;
use function Diff\getValue;
use function Diff\toStr;

function json(array $data): string
{
    return json_encode($data, JSON_PRETTY_PRINT);
}

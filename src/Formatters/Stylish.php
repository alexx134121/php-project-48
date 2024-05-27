<?php

namespace  Differ\Formatters;

use function Differ\Differ\getValue;
use function Differ\Differ\getChild;
use function Differ\Differ\getKey;
use function Differ\Differ\getOldValue;
use function Differ\Differ\toStr;

const STYLISH_FORMAT = [
    'add' => '+ ',
    'del' => '- ',
    'without_changes' => '  ',
    'update' => '  ',
    'padding' => '  ',
];

function stylish($data, int $nested = 1): string
{
    return trim(iter($data, $nested));
}

function iter($data, int $nested = 1): string
{
    if (!is_array($data)) {
        return toStr($data) == '' ? ' ' : " " . toStr($data);
    }
    $padding = str_repeat(' ', 4 * $nested - 2);
    $paddingRight = str_repeat(' ', 4 * $nested - 4) . "}";
    $res = array_reduce($data, function (string $carry, array $item) use ($nested, $padding) {
        $type = \Differ\Differ\getType($item);
        $symbol = STYLISH_FORMAT[$type];
        $key = getKey($item);
        if (is_null(getValue($item)) && !empty(getChild($item))) {
            $child = iter(getChild($item), $nested + 1);
            $carry .= "{$padding}{$symbol}{$key}:{$child}\n";
            return $carry;
        }
        $oldVal = iter(getOldValue($item), $nested + 1);
        $val = iter(getValue($item), $nested + 1);
        if ($type == 'update') {
            $delSymbol = STYLISH_FORMAT['del'];
            $addSymbol = STYLISH_FORMAT['add'];
            $carry .= "{$padding}{$delSymbol}{$key}:{$oldVal}\n";
            $carry .= "{$padding}{$addSymbol}{$key}:{$val}\n";
            return $carry;
        }
        $carry .= "{$padding}{$symbol}{$key}:{$val}\n";
        return $carry;
    }, '');
    return " {\n" . $res . "$paddingRight";
}

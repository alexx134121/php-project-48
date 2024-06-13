<?php

namespace  Differ\Formatters\Stylish;

use function Differ\Differ\getTypeNode;
use function Differ\Differ\getValue;
use function Differ\Differ\getChild;
use function Differ\Differ\getKey;
use function Differ\Differ\getOldValue;
use function Differ\Differ\toStr;

const STYLISH_FORMAT_TEMPLATES = [
    'add' => '+ ',
    'del' => '- ',
    'without_changes' => '  ',
    'update' => '  ',
    'padding' => '  ',
];

function format(array $data, int $nested = 1): string
{
    return trim(iter($data, $nested));
}

function iter(mixed $data, int $nested = 1): string
{
    if (!is_array($data)) {
        return toStr($data) == '' ? ' ' : " " . toStr($data);
    }
    $padding = str_repeat(' ', 4 * $nested - 2);
    $paddingRight = "\n" . str_repeat(' ', 4 * $nested - 4) . "}";
    $res = array_map(function (array $item) use ($nested, $padding) {
        $type = getTypeNode($item);
        $symbol = STYLISH_FORMAT_TEMPLATES[$type];
        $key = getKey($item);
        if (is_null(getValue($item)) && getChild($item) != []) {
            $child = iter(getChild($item), $nested + 1);
            return "{$padding}{$symbol}{$key}:{$child}";
        }
        $oldVal = iter(getOldValue($item), $nested + 1);
        $val = iter(getValue($item), $nested + 1);
        if ($type == 'update') {
            $delSymbol = STYLISH_FORMAT_TEMPLATES['del'];
            $addSymbol = STYLISH_FORMAT_TEMPLATES['add'];
            return "{$padding}{$delSymbol}{$key}:{$oldVal}\n{$padding}{$addSymbol}{$key}:{$val}";
        }
        return "{$padding}{$symbol}{$key}:{$val}";
    }, $data);
    return " {\n" . implode("\n", $res) . "$paddingRight";
}

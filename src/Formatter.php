<?php

namespace Diff;

const STYLISH = 'stylish';

const STYLISH_FORMAT = [
    'add' => '+ ',
    'del' => '- ',
    'without_changes' => '  ',
    'padding' => '  ',
];

function format(array $data, string $type = STYLISH): string
{
    if ($type == STYLISH) {
        return "{\n" . stylish($data) . "}";
    }
    throw new \Exception("Формат $type недоступен");
}
function stylish(array $data, int $nested = 1): string
{
    $padding = str_repeat(' ', 4 * $nested - 2);
    return array_reduce($data, function (string $carry, array $item) use ($nested, $padding) {
        if (is_null(getValue($item)) && !empty(getChild($item))) {
            $child = stylish(getChild($item), $nested + 1);
            $paddingRight = str_repeat(' ', 4 * $nested) . "}";
            $str = $padding . STYLISH_FORMAT[getType($item)] . getKey($item) . ": {\n" . $child . $paddingRight;
            $carry .= $str . "\n";
            return $carry;
        }
        $val = empty(toStr(getValue($item))) ? '' : " " . toStr(getValue($item));
        $carry .= $padding . STYLISH_FORMAT[getType($item)] . getKey($item) . ":" . $val . "\n";

        return $carry;
    }, '');
}

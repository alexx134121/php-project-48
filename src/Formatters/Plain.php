<?php

namespace Differ\Formatters\Plain;

use function Differ\Differ\getChild;
use function Differ\Differ\getKey;
use function Differ\Differ\getOldValue;
use function Differ\Differ\getTypeNode;
use function Differ\Differ\getValue;
use function Differ\Differ\toStr;

const PLAIN_FORMAT_TEMPLATES = [
    'add' => "Property '%s' was added with value: %s",
    'del' => "Property '%s' was removed",
    'update' => "Property '%s' was updated. From %s to %s",
];
function format(array $data, string $path = ''): string
{
    $result = array_map(function (array $item) use ($path) {
        $key = getKey($item);
        $type = getTypeNode($item);
        $nodeValue = getValue($item);
        $currentPath = $path === '' ? $key : "$path.$key";
        $value = isComplexValue($nodeValue) || isComplexValue(getChild($item))
            ? '[complex value]'
            : toStr($nodeValue);
        $oldValue = isComplexValue(getOldValue($item)) ?
            '[complex value]' :
            toStr(getOldValue($item));
        if (getChild($item) !== [] && $type == 'without_changes') {
            $child = format(getChild($item), $currentPath);
            return $child;
        } elseif ($type == 'without_changes') {
            return [];
        }
        $valueString = is_string($nodeValue) ? "'$value'" : $value;
        $oldValueString = is_string(getOldValue($item)) ? "'$oldValue'" : $oldValue;
        if ($type == 'update') {
            $str = sprintf(PLAIN_FORMAT_TEMPLATES[$type], $currentPath, $oldValueString, $valueString);
        } else {
            $str = sprintf(PLAIN_FORMAT_TEMPLATES[$type], $currentPath, $valueString, $oldValueString);
        }
        return $str;
    }, $data);
    return implode("\n", array_filter($result, fn($item) => $item !== []));
}

function isComplexValue(mixed $item): bool
{
    return is_array($item) && $item !== [];
}

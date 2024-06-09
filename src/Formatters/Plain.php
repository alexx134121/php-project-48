<?php

namespace Differ\Formatters\Plain;

use function Differ\Differ\Differ\getChild;
use function Differ\Differ\Differ\getKey;
use function Differ\Differ\Differ\getOldValue;
use function Differ\Differ\Differ\getTypeNode;
use function Differ\Differ\Differ\getValue;
use function Differ\Differ\Differ\toStr;

const PLAIN_FORMAT = [
    'add' => "Property '%s' was added with value: %s",
    'del' => "Property '%s' was removed",
    'update' => "Property '%s' was updated. From %s to %s",
];

function plain(array $data, string $path = ''): string
{
    $result = array_reduce($data, function (array $carry, array $item) use ($path) {
        $key = getKey($item);
        $type = getTypeNode($item);
        $currentPath = $path === '' ? $key : "$path.$key";
        $value = isComplexValue(getValue($item)) || isComplexValue(getChild($item))
            ? '[complex value]'
            : toStr(getValue($item));
        $oldValue = isComplexValue(getOldValue($item)) ?
            '[complex value]' :
            toStr(getOldValue($item));
        if (getChild($item) !== [] && $type == 'without_changes') {
            $child = plain(getChild($item), $currentPath);
            return array_merge($carry, [$child]);
        } elseif ($type == 'without_changes') {
            return $carry;
        }
        $valueString = is_string(getValue($item)) ? "'$value'" : $value;
        $oldValueString = is_string(getOldValue($item)) ? "'$oldValue'" : $oldValue;
        if ($type == 'update') {
            $str = sprintf(PLAIN_FORMAT[$type], $currentPath, $oldValueString, $valueString);
        } else {
            $str = sprintf(PLAIN_FORMAT[$type], $currentPath, $valueString, $oldValueString);
        }
        return array_merge($carry, [$str]);
    }, []);
    return implode("\n", $result);
}

function isComplexValue(mixed $item): bool
{
    return is_array($item) && $item !== [];
}

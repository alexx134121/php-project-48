<?php

namespace Differ\Formatters;

use function Differ\Differ\getChild;
use function Differ\Differ\getKey;
use function Differ\Differ\getValue;
use function Differ\Differ\toStr;

const PLAIN_FORMAT = [
    'add' => "Property '%s' was added with value: %s",
    'del' => "Property '%s' was removed",
    'update' => "Property '%s' was updated. From %s to %s",
];

function plain(array $data, string $path = ''): string
{
    $result = array_reduce($data, function (array $carry, array $item) use ($path) {
        $key = getKey($item);
        $currentPath = empty($path) ? $key : "$path.$key";
        $value = isComplexValue(getValue($item)) || isComplexValue(getChild($item))
            ? '[complex value]'
            : toStr(getValue($item));
        $oldValue = isComplexValue(\Differ\Differ\getOldValue($item)) ?
            '[complex value]' :
            toStr(\Differ\Differ\getOldValue($item));
        if (!empty(getChild($item)) && \Differ\Differ\getType($item) == 'without_changes') {
            $child = plain(getChild($item), $currentPath);
            $carry [] = $child;
            return $carry;
        } elseif (\Differ\Differ\getType($item) == 'without_changes') {
            return $carry;
        }
        $valueString = is_string(getValue($item)) ? "'$value'" : $value;
        $oldValueString = is_string(\Differ\Differ\getOldValue($item)) ? "'$oldValue'" : $oldValue;
        if (\Differ\Differ\getType($item) == 'update') {
            $str = sprintf(PLAIN_FORMAT[\Differ\Differ\getType($item)], $currentPath, $oldValueString, $valueString);
        } else {
            $str = sprintf(PLAIN_FORMAT[\Differ\Differ\getType($item)], $currentPath, $valueString, $oldValueString);
        }
        $carry[] = $str;
        return $carry;
    }, []);
    return implode("\n", $result);
}

function isComplexValue(mixed $item): bool
{
    return is_array($item) && !empty($item);
}

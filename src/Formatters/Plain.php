<?php

namespace Diff\Formatters;

use function Diff\getChild;
use function Diff\getKey;
use function Diff\getValue;
use function Diff\toStr;

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
        $oldValue = isComplexValue(\Diff\getOldValue($item)) ? '[complex value]' : toStr(\Diff\getOldValue($item));
        if (!empty(getChild($item)) && \Diff\getType($item) == 'without_changes') {
            $child = plain(getChild($item), $currentPath);
            $carry [] = $child;
            return $carry;
        } elseif (\Diff\getType($item) == 'without_changes') {
            return $carry;
        }
        $valueString = is_string(getValue($item)) ? "'$value'" : $value;
        $oldValueString = is_string(\Diff\getOldValue($item)) ? "'$oldValue'" : $oldValue;
        if (\Diff\getType($item) == 'update') {
            $str = sprintf(PLAIN_FORMAT[\Diff\getType($item)], $currentPath, $oldValueString, $valueString);
        } else {
            $str = sprintf(PLAIN_FORMAT[\Diff\getType($item)], $currentPath, $valueString, $oldValueString);
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

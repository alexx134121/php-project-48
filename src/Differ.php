<?php

namespace Differ\Differ;

use function Differ\Formatters\Formatters\format;
use function Differ\Parsers\Parsers\parserData;
use function Functional\sort;

use const Differ\Formatters\Formatters\STYLISH;

const ADD = 'add';
const DELETE = 'del';
const WITHOUT_CHANGES = 'without_changes';
const UPDATE = 'update';

function makeNode(mixed $val, string $key, array $child, string $type, mixed $oldValue = null): array
{
    return
        [
            'key_name' => $key,
            'type' => $type,
            'value' => $val,
            'old_value' => $oldValue,
            'child' => $child
        ];
}

function getKey(array $node): string
{
    return $node['key_name'];
}

function getOldValue(array $node): mixed
{
    return $node['old_value'];
}

function getTypeNode(array $node): string
{
    return $node['type'];
}

function getValue(array $node): mixed
{
    return $node['value'];
}

function getChild(array $node): array
{
    return $node['child'];
}


function genDiff(string $pathToFile1, string $pathToFile2, string $format = STYLISH): string
{
    $data1 = parserData($pathToFile1);
    $data2 = parserData($pathToFile2);
    return format(makeTree($data1, $data2), $format);
}

function makeTree(array $old, array $new): array
{
    $merge = array_merge(array_keys($old), array_keys($new));
    $keys = array_unique($merge);
    $sortedKeys = sort($keys, fn($item1, $item2) => $item1 <=> $item2);
    return array_reduce($sortedKeys, function ($carry, $key) use ($old, $new) {
        if (array_key_exists($key, $old) && !array_key_exists($key, $new)) {
            if (is_array($old[$key])) {
                return array_merge($carry, [makeNode(null, $key, makeTree($old[$key], $old[$key]), DELETE)]);
            }
            return array_merge($carry, [makeNode($old[$key], $key, [], DELETE)]);
        }
        if (!array_key_exists($key, $old) && array_key_exists($key, $new)) {
            if (is_array($new[$key])) {
                return array_merge($carry, [makeNode(null, $key, makeTree($new[$key], $new[$key]), ADD)]);
            }
            return array_merge($carry, [makeNode($new[$key], $key, [], ADD)]);
        }
        if (is_array($old[$key]) && is_array($new[$key])) {
            return array_merge($carry, [makeNode(null, $key, makeTree($old[$key], $new[$key]), WITHOUT_CHANGES)]);
        }
        if ($old[$key] === $new[$key]) {
            return array_merge($carry, [makeNode($new[$key], $key, [], WITHOUT_CHANGES)]);
        }

        if (is_array($old[$key])) {
            return array_merge($carry, [makeNode($new[$key], $key, [], UPDATE, makeTree($old[$key], $old[$key]))]);
        }
        if (is_array($new[$key])) {
            return array_merge($carry, [makeNode(makeTree($new[$key], $new[$key]), $key, [], UPDATE, $old[$key])]);
        }
        return array_merge($carry, [makeNode($new[$key], $key, [], UPDATE, $old[$key])]);
    }, []);
}

function toStr(mixed $value): string
{
    return is_null($value) ? 'null' : str_replace("'", '', trim(var_export($value, true)));
}

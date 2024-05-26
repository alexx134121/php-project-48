<?php

namespace Diff;

use function Diff\Formatters\format;

const DELETED = '- ';
const ADDED = '+ ';
const EQUALS = '  ';
const REPLACER = '  ';
function run(): void
{
    $doc = <<<DOC
Generate diff

Usage:
  gendiff (-h|--help)
  gendiff (-v|--version)
  gendiff [--format <fmt>] <firstFile> <secondFile>

Options:
  -h --help                     Show this screen
  -v --version                  Show version
  --format <fmt>                Report format [default: stylish]
DOC;

    $result = \Docopt::handle($doc, ['version' => 'cli 1.0']);
    $args = $result->args;
    $diff = genDiff($args['<firstFile>'], $args['<secondFile>'], $args['--format']);
    print_r($diff);
}

function node(mixed $val, string $key, array $child, string $type, mixed $oldValue = null): array
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

function getType(array $node): string
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

function genDiffFile(string $pathToFile1, string $pathToFile2): array
{
    $data1 = parser($pathToFile1);
    $data2 = parser($pathToFile2);
    return getStructure($data1, $data2);
}

function genDiff(string $pathToFile1, string $pathToFile2, string $format): string
{
    return format(genDiffFile($pathToFile1, $pathToFile2), $format);
}

function getStructure(array $old, array $new): array
{
    $merge = array_merge(array_keys($old), array_keys($new));
    $unique = array_unique($merge);
    $keys = $unique;
    $sortedKeys = immutableSort($keys);
    return array_reduce($sortedKeys, function ($carry, $key) use ($old, $new) {
        if (array_key_exists($key, $old) && !array_key_exists($key, $new)) {
            if (is_array($old[$key])) {
                $carry[] = node(null, $key, getStructure($old[$key], $old[$key]), 'del');
                return $carry;
            }
            $carry[] = node($old[$key], $key, [], 'del');
            return $carry;
        }
        if (!array_key_exists($key, $old) && array_key_exists($key, $new)) {
            if (is_array($new[$key])) {
                $carry[] = node(null, $key, getStructure($new[$key], $new[$key]), 'add');
                return $carry;
            }
            $carry[] = node($new[$key], $key, [], 'add');
            return $carry;
        }
        if (is_array($old[$key]) && is_array($new[$key])) {
            $carry[] = node(null, $key, getStructure($old[$key], $new[$key]), 'without_changes');
            return $carry;
        }
        if ($old[$key] == $new[$key]) {
            $carry[] = node($new[$key], $key, [], 'without_changes');
            return $carry;
        }
        ///
        if (is_array($old[$key])) {
            $carry[] = node($new[$key], $key, [], 'update', getStructure($old[$key], $old[$key]));
            return $carry;
        }
        if (is_array($new[$key])) {
            $carry[] = node(getStructure($new[$key], $new[$key]), $key, [], 'update', $old[$key]);
            return $carry;
        }
        ///
        $carry[] = node($new[$key], $key, [], 'update', $old[$key]);
        return $carry;
    }, []);
}

function toStr(mixed $value): string
{
    $result = is_null($value) ? 'null' : str_replace("'", '', trim(var_export($value, true)));
    return $result;
}

function immutableSort(array $data): array
{
    sort($data);
    return $data;
}

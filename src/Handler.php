<?php

namespace Diff;

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
    $diff = genDiffFile($args['<firstFile>'], $args['<secondFile>']);
    print_r(format($diff, $args['--format']));
}

function node(mixed $val, string $key, array $child, string $type): array
{
    return
        [
            'key_name' => $key,
            'type' => $type,
            'value' => $val,
            'child' => $child
        ];
}

function getKey(array $node): string
{
    return $node['key_name'];
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
    return genDiff($data1, $data2);
}

function genDiff(array $data1, $data2)
{
    $merge = array_merge(array_keys($data1), array_keys($data2));
    $unique = array_unique($merge);
    $keys = $unique;
    $sortedKeys = immutableSort($keys);
    $res = array_reduce($sortedKeys, function ($carry, $key) use ($data1, $data2) {
        if (array_key_exists($key, $data1) && !array_key_exists($key, $data2)) {
            if (is_array($data1[$key])) {
                $carry[] = node(null, $key, genDiff($data1[$key], $data1[$key]), 'del');
                return $carry;
            }
            $carry[] = node($data1[$key], $key, [], 'del');
            return $carry;
        }
        if (!array_key_exists($key, $data1) && array_key_exists($key, $data2)) {
            if (is_array($data2[$key])) {
                $carry[] = node(null, $key, genDiff($data2[$key], $data2[$key]), 'add');
                return $carry;
            }
            $carry[] = node($data2[$key], $key, [], 'add');
            return $carry;
        }
        if (is_array($data1[$key]) && is_array($data2[$key])) {
            $carry[] = node(null, $key, genDiff($data1[$key], $data2[$key]), 'without_changes');
            return $carry;
        }
        if ($data1[$key] == $data2[$key]) {
            $carry[] = node($data2[$key], $key, [], 'without_changes');
            return $carry;
        }
        $carry[] = is_array($data1[$key]) ?
            node(null, $key, genDiff($data1[$key], $data1[$key]), 'del') :
            node($data1[$key], $key, [], 'del');
        $carry[] = is_array($data2[$key]) ?
            node(null, $key, genDiff($data2[$key], $data2[$key]), 'add') :
            node($data2[$key], $key, [], 'add');
        return $carry;
    }, []);
    return $res;
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

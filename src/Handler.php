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
    genDiff($args['<firstFile>'], $args['<secondFile>']);
}
function genDiff(string $pathToFile1, string $pathToFile2): string
{

    $data1 = parser($pathToFile1);
    $data2 = parser($pathToFile2);
    $unique = array_unique(array_merge($data1, $data2));
    $keys = array_keys($unique);
    $sortedKeys = immutableSort($keys);
    $res = array_reduce($sortedKeys, function ($carry, $key) use ($data1, $data2) {
        if (isset($data1[$key]) && !isset($data2[$key])) {
            $carry[] = REPLACER . DELETED . "$key: " . toStr($data1[$key]);
            return $carry;
        }
        if (!isset($data1[$key]) && isset($data2[$key])) {
            $carry[] = REPLACER . ADDED . "$key: " . toStr($data2[$key]);
            return $carry;
        }
        if ($data1[$key] == $data2[$key]) {
            $carry[] = REPLACER . EQUALS . "$key: " . toStr($data1[$key]);
            return $carry;
        }
        $carry[] = REPLACER . DELETED . "$key: " . toStr($data1[$key]);
        $carry[] = REPLACER . ADDED . "$key: " . toStr($data2[$key]);
        return $carry;
    }, []);
    return "{\n" . implode("\n", $res) . "\n}";
}
function toStr(mixed $value): string
{
    return str_replace("'", '', trim(var_export($value, true)));
}

function immutableSort(array $data): array
{
    sort($data);
    return $data;
}

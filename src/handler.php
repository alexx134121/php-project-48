<?php

namespace Diff;

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
function getDataFromFiles(string $pathToFile1, string $pathToFile2): array
{
    $path1 = realpath($pathToFile1);
    $path2 = realpath($pathToFile2);
    if (!$path1 || !$path2) {
        $file = $path1 ? $pathToFile2 : $pathToFile1;
        throw new \Exception("Не найден путь к файлу $file");
    }
    $json1 = json_decode(file_get_contents($path1), true, flags: JSON_THROW_ON_ERROR);
    $json2 = json_decode(file_get_contents($path2), true, flags: JSON_THROW_ON_ERROR);
    return [$json1, $json2];
}
function genDiff(string $pathToFile1, string $pathToFile2): string
{
    try {
        [$json1, $json2] = getDataFromFiles($pathToFile1, $pathToFile2);
    } catch (\Exception $e) {
        echo $e->getMessage();
        return $e->getMessage();
    }
    $keysJson1 = array_keys($json1);
    $keysJson2 = array_keys($json2);
    $deleted = array_filter($keysJson1, fn($item) => !in_array($item, $keysJson2));
    $added = array_filter($keysJson2, fn($item) => !in_array($item, $keysJson1));
    $modifed = array_reduce($keysJson1, function ($acc, $key) use ($json1, $json2, $keysJson2, $keysJson1) {
        if (!isset($json2[$key]) || !isset($json1[$key])) {
            return $acc;
        }
        if ($json1[$key] === $json2[$key]) {
            $acc[] = "  {$key}: {$json1[$key]}";
            return $acc;
        }
        $acc[] = "- $key : {$json1[$key]}";
        $acc[] = "+ $key : {$json2[$key]}";
        return $acc;
    }, []);

    $result = "{\n" . implode("\n", array_map(fn($key) => "- {$deleted[$key]}: " . var_export($json1[$deleted[$key]], true), array_keys($deleted))) . "\n";
    $result .= implode("\n", array_map(fn($key) => "+ {$added[$key]}: " . var_export($json2[$added[$key]], true), array_keys($added))) . "\n";
    $result .= implode("\n", $modifed) . "\n}";

    return $result;
}

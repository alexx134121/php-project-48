<?php

namespace Differ;

use function Differ\Differ\genDiff;

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

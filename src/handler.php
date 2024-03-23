<?php

namespace Diff;

function run()
{
    $doc = <<<DOC
Generate diff

Usage:
  gendiff (-h|--help)
  gendiff (-v|--version)

Options:
  -h --help                     Show this screen
  -v --version                  Show version
DOC;
    $args = \Docopt::handle($doc, ['version' => 'cli 1.0']);
}

<?php

namespace Diff\Tests;

use PHPUnit\Framework\TestCase;
use function Diff\genDiff;

class JSONParserTest extends TestCase
{
    public function test_diff()
    {
        $path1='/home/alexx/project/php-project-48/tests/fixtures/file1.json';
        $path2='tests/fixtures/file2.json';
        $result = genDiff($path1,$path2);
        $expected = file_get_contents('tests/fixtures/diff_result');

        $this->assertEquals($expected,$result);
    }

    public function test_file_not_exists()
    {
        $path1='tests/fixtures/file2.json';
        $path2='fixtures/file2111.json';
        $result = genDiff($path1,$path2);
        $this->assertEquals('Не найден путь к файлу fixtures/file2111.json',$result);
    }
}
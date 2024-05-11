<?php

namespace Diff\Tests;

use PHPUnit\Framework\TestCase;
use function Diff\genDiff;
use function Diff\getDataFromFiles;
use function Diff\immutableSort;
use function Diff\toStr;

class JSONParserTest extends TestCase
{

    /**
     * @covers \Diff\getDataFromFiles()
     */

    public function test_file_not_exists()
    {
        $path1 = 'tests/fixtures/file2.json';
        $path2 = 'fixtures/file2111.json';
        $this->expectException(\Exception::class);
        $result = getDataFromFiles($path1, $path2);
        $this->assertEquals('Не найден путь к файлу fixtures/file2111.json', $result);
    }

    /**
     * @covers       \Diff\toStr()
     * @dataProvider string_test_data_provider
     */
    public function test_to_string($val, $excepted)
    {
        $this->assertEquals($excepted, toStr($val));
    }

    /**
     * @covers \Diff\immutableSort()
     */
    public function test_immutable_sort()
    {
        $data = ['a', 'b', 'd', 'c'];
        $expected = ['a', 'b', 'c', 'd'];
        $result = immutableSort($data);
        $this->assertEquals($expected, $result);
        $this->assertNotEquals($data, $result);
    }

    /**
     * @covers \Diff\getDataFromFiles()
     * @covers \Diff\immutableSort()
     * @covers \Diff\toStr()
     * @covers \Diff\genDiff()
     */
    public function test_diff()
    {
        $path1 = __DIR__.'/fixtures/file1.json';
        $path2 = 'tests/fixtures/file2.json';
        $result = genDiff($path1, $path2);
        $expected = file_get_contents('tests/fixtures/diff_result');

        $this->assertEquals($expected, $result);

        $path1 = __DIR__.'/fixtures/file1.json';
        $path2 = 'tests/fixtures/file2111.json';
        $result = genDiff($path1, $path2);
        $this->assertEquals('Не найден путь к файлу tests/fixtures/file2111.json',$result);
    }

    public function string_test_data_provider()
    {
        return [
            [true, 'true'],
            [null, 'NULL'],
            [4, 4],
            ['test', 'test'],
        ];
    }
}
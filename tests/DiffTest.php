<?php

namespace Diff\Tests;

use PHPUnit\Framework\TestCase;
use function Diff\genDiff;
use function Diff\getDataFromFile;
use function Diff\immutableSort;
use function Diff\toStr;

class DiffTest extends TestCase
{

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
     * @covers \Diff\getDataFromFile()
     * @covers \Diff\immutableSort()
     * @covers \Diff\toStr()
     * @covers \Diff\genDiff()
     * @covers \Diff\jsonParser()
     * @covers \Diff\parser()
     */
    public function test_diff()
    {
        $path1 = __DIR__ . '/fixtures/file1.json';
        $path2 = 'tests/fixtures/file2.json';
        $result = genDiff($path1, $path2);
        $expected = file_get_contents('tests/fixtures/diff_result');

        $this->assertEquals($expected, $result);

        $path1 = __DIR__ . '/fixtures/file1.json';
        $path2 = 'tests/fixtures/file2111.json';
        $this->expectException(\Exception::class);
        $result = genDiff($path1, $path2);

        $path1 = __DIR__ . '/fixtures/file1.yml';
        $path2 = 'tests/fixtures/file2.yaml';
        $result = genDiff($path1, $path2);
        $expected = file_get_contents('tests/fixtures/diff_result');

        $this->assertEquals($expected, $result);

        $path1 = __DIR__ . '/fixtures/file1.yml';
        $path2 = 'tests/fixtures/file2111.yaml';
        $this->expectException(\Exception::class);
        $result = genDiff($path1, $path2);

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
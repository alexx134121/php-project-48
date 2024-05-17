<?php

namespace Diff\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;
use function Diff\format;
use function Diff\genDiffFile;
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
     * @covers \Diff\genDiffFile()
     * @covers \Diff\jsonParser()
     * @covers \Diff\parser()
     */
    public function test_diff()
    {
        $path1 = __DIR__ . '/fixtures/file1.json';
        $path2 = 'tests/fixtures/file2.json';
        $diff = genDiffFile($path1, $path2);
        $result = format($diff);
        $expected = file_get_contents('tests/fixtures/diff_result');

        $this->assertEquals($expected, $result);

        $path1 = __DIR__ . '/fixtures/file1.json';
        $path2 = '/tests/fixtures/file2111.json';
        $this->expectException(\Exception::class);
        $result = genDiffFile($path1, $path2);

        $path1 = __DIR__ . '/fixtures/file1.yml';
        $path2 = '/tests/fixtures/file2.yaml';
        $result = genDiffFile($path1, $path2);
        $expected = file_get_contents('tests/fixtures/diff_result');

        $this->assertEquals($expected, $result);

        $path1 = __DIR__ . '/fixtures/file1.yml';
        $path2 = '/tests/fixtures/file2111.yaml';
        $this->expectException(\Exception::class);
        $result = genDiffFile($path1, $path2);
        $this->assertEquals($expected,$result);

    }
    /**
     * @covers \Diff\getDataFromFile()
     * @covers \Diff\immutableSort()
     * @covers \Diff\toStr()
     * @covers \Diff\genDiffFile()
     * @covers \Diff\jsonParser()
     * @covers \Diff\parser()
     */

    public function test_nested_structure()
    {
        $path1 = __DIR__ . '/fixtures/nested_file1.json';
        $path2 = 'tests/fixtures/nested_file2.json';
        $result = genDiffFile($path1, $path2);

        $expected =json_decode(file_get_contents(__DIR__ . '/fixtures/nested_structure.json'),true);
        $this->assertEquals($expected,$result);

        $path1 = __DIR__ . '/fixtures/nested_file1.yaml';
        $path2 = 'tests/fixtures/nested_file2.yaml';
        $result = genDiffFile($path1, $path2);
        $this->assertEquals($expected,$result);
    }

    /**
     * @covers \Diff\getDataFromFile()
     * @covers \Diff\immutableSort()
     * @covers \Diff\toStr()
     * @covers \Diff\genDiffFile()
     * @covers \Diff\jsonParser()
     * @covers \Diff\parser()
     */
    public function test_plan_structure()
    {
        $path1 = __DIR__ . '/fixtures/file1.json';
        $path2 = 'tests/fixtures/file2.json';
        $result = genDiffFile($path1, $path2);
        $expected =json_decode(file_get_contents(__DIR__ . '/fixtures/plan_structure.json'),true);
        $this->assertEquals($expected,$result);
    }
    /**
     * @covers \Diff\getDataFromFile()
     * @covers \Diff\immutableSort()
     * @covers \Diff\toStr()
     * @covers \Diff\genDiffFile()
     * @covers \Diff\jsonParser()
     * @covers \Diff\parser()
     * @covers \Diff\format()
     */
    public function test_format_plan_structure()
    {
        $path1 = __DIR__ . '/fixtures/file1.json';
        $path2 = 'tests/fixtures/file2.json';
        $result = genDiffFile($path1, $path2);
        $formatted = format($result, 'stylish');
        $excepted = file_get_contents(__DIR__ . '/fixtures/diff_result');
        $this->assertEquals($excepted, $formatted);

    }

    /**
     * @covers \Diff\getDataFromFile()
     * @covers \Diff\immutableSort()
     * @covers \Diff\toStr()
     * @covers \Diff\genDiffFile()
     * @covers \Diff\jsonParser()
     * @covers \Diff\parser()
     * @covers \Diff\format()
     */
    public function test_nested_diff()
    {
        $path1 = __DIR__ . '/fixtures/nested_file1.json';
        $path2 = 'tests/fixtures/nested_file2.json';
        $result = genDiffFile($path1, $path2);
        $formatted = format($result, 'stylish');
        $expected = file_get_contents('tests/fixtures/nested_diff_result');
        $this->assertEquals($expected, $formatted);

        $path1 = __DIR__ . '/fixtures/nested_file1.yaml';
        $path2 = 'tests/fixtures/nested_file2.yaml';
        $result = genDiffFile($path1, $path2);
        $formatted = format($result, 'stylish');
        $this->assertEquals($expected, $formatted);

    }
    public function string_test_data_provider()
    {
        return [
            [true, 'true'],
            [null, 'null'],
            [4, 4],
            ['test', 'test'],
        ];
    }
}
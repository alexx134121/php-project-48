<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;
use function Differ\Formatters\format;
use function Differ\Differ\genDiff;
use function Differ\Differ\genDiffFile;
use function Differ\Differ\getChild;
use function Differ\Differ\getKey;
use function Differ\Differ\getOldValue;
use function Differ\Differ\getValue;
use function Differ\Differ\immutableSort;
use function Differ\Differ\node;
use function Differ\Differ\toStr;

class DiffTest extends TestCase
{

    /**
     * @covers       \Differ\Differ\toStr()
     * @dataProvider string_test_data_provider
     */
    public function test_to_string($val, $excepted)
    {
        $this->assertEquals($excepted, toStr($val));
    }

    /**
     * @covers \Differ\Differ\immutableSort()
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
     * @covers \Differ\getDataFromFile()
     * @covers \Differ\Differ\immutableSort()
     * @covers \Differ\Differ\toStr()
     * @covers \Differ\Differ\genDiffFile()
     * @covers \Differ\jsonParser()
     * @covers \Differ\parser()
     * @covers \Differ\Differ\getStructure()
     */
    public function test_diff()
    {
        $path1 = __DIR__ . '/fixtures/file1.json';
        $path2 = __DIR__ .'/../'.'tests/fixtures/file2.json';
        $diff = genDiffFile($path1, $path2);
        $result = format($diff);
        $expected = file_get_contents(__DIR__ .'/../'.'tests/fixtures/diff_result');

        $this->assertEquals($expected, $result);

        $path1 = __DIR__ . '/fixtures/file1.json';
        $path2 = __DIR__ .'/../'.'/tests/fixtures/file2111.json';
        $this->expectException(\Exception::class);
        $result = genDiffFile($path1, $path2);

        $path1 = __DIR__ . '/fixtures/file1.yml';
        $path2 = __DIR__ .'/../'.'/tests/fixtures/file2.yaml';
        $result = genDiffFile($path1, $path2);
        $expected = file_get_contents(__DIR__ .'/../'.'tests/fixtures/diff_result');

        $this->assertEquals($expected, $result);

        $path1 = __DIR__ . '/fixtures/file1.yml';
        $path2 = '/tests/fixtures/file2111.yaml';
        $this->expectException(\Exception::class);
        $result = genDiffFile($path1, $path2);
        $this->assertEquals($expected,$result);

    }
    /**
     * @covers \Differ\getDataFromFile()
     * @covers \Differ\Differ\immutableSort()
     * @covers \Differ\Differ\toStr()
     * @covers \Differ\Differ\genDiffFile()
     * @covers \Differ\jsonParser()
     * @covers \Differ\parser()
     * @covers \Differ\Differ\genDiff()
     * @covers \Differ\Differ\getStructure()
     * @covers \Differ\Differ\getOldValue()
     */

    public function test_nested_structure()
    {
        $path1 = __DIR__ . '/fixtures/nested_file1.json';
        $path2 = __DIR__ .'/../'.'tests/fixtures/nested_file2.json';
        $result = genDiffFile($path1, $path2);
        $expected =json_decode(file_get_contents(__DIR__ . '/fixtures/nested_structure_2.json'),true);
        $this->assertEquals($expected,$result);

        $path1 = __DIR__ . '/fixtures/nested_file1.yaml';
        $path2 = __DIR__ .'/../'.'tests/fixtures/nested_file2.yaml';
        $result = genDiffFile($path1, $path2);
        $this->assertEquals($expected,$result);
    }

    /**
     * @covers \Differ\getDataFromFile()
     * @covers \Differ\Differ\immutableSort()
     * @covers \Differ\Differ\toStr()
     * @covers \Differ\Differ\genDiffFile()
     * @covers \Differ\jsonParser()
     * @covers \Differ\parser()
     * @covers \Differ\Differ\getStructure()
     */
    public function test_plan_structure()
    {
        $path1 = __DIR__ . '/fixtures/file1.json';
        $path2 =  __DIR__ .'/../tests/fixtures/file2.json';
        $result = genDiffFile($path1, $path2);
        $expected =json_decode(file_get_contents(__DIR__ . '/fixtures/plan_structure_1.json'),true);
        $this->assertEquals($expected,$result);
    }
    /**
     * @covers \Differ\getDataFromFile()
     * @covers \Differ\Differ\immutableSort()
     * @covers \Differ\Differ\toStr()
     * @covers \Differ\Differ\genDiffFile()
     * @covers \Differ\jsonParser()
     * @covers \Differ\parser()
     * @covers \Differ\Formatters\format()
     * @covers \Differ\Differ\getStructure()
     */
    public function test_format_plan_structure()
    {
        $path1 = __DIR__ . '/fixtures/file1.json';
        $path2 = __DIR__ .'/../'.'tests/fixtures/file2.json';
        $result = genDiffFile($path1, $path2);
        $formatted = format($result, 'stylish');
        $excepted = file_get_contents(__DIR__ . '/fixtures/diff_result');
        $this->assertEquals($excepted, $formatted);

    }

    /**
     * @covers \Differ\getDataFromFile()
     * @covers \Differ\Differ\immutableSort()
     * @covers \Differ\Differ\toStr()
     * @covers \Differ\Differ\genDiffFile()
     * @covers \Differ\jsonParser()
     * @covers \Differ\parser()
     * @covers \Differ\Formatters\format()
     * @covers \Differ\Formatters\stylish()
     * @covers \Differ\Formatters\iter()
     * @covers \Differ\Differ\getStructure()
     * @covers \Differ\Differ\genDiff()
     */
    public function test_nested_diff()
    {
        $path1 = __DIR__ . '/fixtures/nested_file1.json';
        $path2 = __DIR__ .'/../'.'tests/fixtures/nested_file2.json';
        $result = genDiffFile($path1, $path2);
        $formatted = format($result, 'stylish');
        $expected = trim(file_get_contents(__DIR__ .'/../'.'tests/fixtures/nested_diff_result'));
        $this->assertEquals($expected, $formatted);

        $path1 = __DIR__ . '/fixtures/nested_file1.yaml';
        $path2 = __DIR__ .'/../'.'tests/fixtures/nested_file2.yaml';
        $result = genDiffFile($path1, $path2);
        $formatted = format($result, 'stylish');
        $this->assertEquals($expected, $formatted);


        $path1 = __DIR__ . '/fixtures/nested_file2.yaml';
        $path2 = __DIR__ .'/../'.'tests/fixtures/nested_file1.yaml';
        $result = genDiffFile($path1, $path2);
        $formatted = format($result, 'stylish');
        $expected = trim(file_get_contents(__DIR__ .'/../'.'tests/fixtures/nested_diff_result_2'));
        $this->assertEquals($expected, $formatted);
        $this->assertEquals($expected,genDiff($path1,$path2,'stylish'));
    }

    /**
     * @covers \Differ\Formatters\format()
     */
    public function test_formatter()
    {
        $this->expectException(\Exception::class);
        format([],'test');
    }

    /**
     * @covers \Differ\Differ\node()
     * @covers \Differ\Differ\getKey()
     * @covers \Differ\Differ\getChild()
     * @covers \Differ\Differ\getType()
     * @covers \Differ\Differ\getValue()
     * @covers \Differ\Differ\getOldValue()
     */

    public function test_node()
    {
        $key = 'key';
        $type = 'add';
        $val = 'val';
        $child = [];
        $oldValue = null;
        $excepted = [
            'key_name' => $key,
            'type' => $type,
            'value' => $val,
            'child' => $child,
            'old_value' => null
        ];
        $node = node($val,$key,$child,$type);
        $this->assertEquals($excepted,$node);
        $this->assertEquals($key,getKey($node));
        $this->assertEquals($val,getValue($node));
        $this->assertEquals($oldValue,getOldValue($node));
        $this->assertEquals($type,\Differ\Differ\getType($node));
        $this->assertEquals($child,getChild($node));
    }
    /**
     * @covers \Differ\getDataFromFile()
     * @covers \Differ\Differ\immutableSort()
     * @covers \Differ\Differ\toStr()
     * @covers \Differ\Differ\genDiffFile()
     * @covers \Differ\jsonParser()
     * @covers \Differ\parser()
     * @covers \Differ\Formatters\format()
     * @covers \Differ\Formatters\plain()
     * @covers \Differ\Formatters\isComplexValue()
     */
    public function test_plain_formatter()
    {
        $path1 = __DIR__ . '/fixtures/nested_file1.json';
        $path2 = __DIR__ . '/fixtures/nested_file2.json';
        $result = genDiffFile($path1, $path2);
        $formatted = format($result, 'plain');
        $expected = file_get_contents( __DIR__ . '/fixtures/diff_plain_format');
        $this->assertEquals($expected, $formatted);
    }
    /**
     * @covers \Differ\getDataFromFile()
     * @covers \Differ\Differ\immutableSort()
     * @covers \Differ\Differ\toStr()
     * @covers \Differ\Differ\genDiffFile()
     * @covers \Differ\jsonParser()
     * @covers \Differ\parser()
     * @covers \Differ\Formatters\format()
     * @covers \Differ\Formatters\json()
     */
    public function test_json_structure()
    {
        $path1 = __DIR__ . '/fixtures/nested_file1.json';
        $path2 = __DIR__ . '/fixtures/nested_file2.json';
        $result = genDiffFile($path1, $path2);
        $formatted = format($result, 'json');
        $expected = file_get_contents( __DIR__ . '/fixtures/diff_json_format');
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
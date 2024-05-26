<?php

namespace Diff\Tests;

use PHPUnit\Framework\TestCase;
use function Diff\Formatters\format;
use function Diff\genDiff;
use function Diff\genDiffFile;
use function Diff\getChild;
use function Diff\getKey;
use function Diff\getOldValue;
use function Diff\getValue;
use function Diff\immutableSort;
use function Diff\node;
use function Diff\run;
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
     * @covers \Diff\getStructure()
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
     * @covers \Diff\getDataFromFile()
     * @covers \Diff\immutableSort()
     * @covers \Diff\toStr()
     * @covers \Diff\genDiffFile()
     * @covers \Diff\jsonParser()
     * @covers \Diff\parser()
     * @covers \Diff\genDiff()
     * @covers \Diff\getStructure()
     * @covers \Diff\getOldValue()
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
     * @covers \Diff\getDataFromFile()
     * @covers \Diff\immutableSort()
     * @covers \Diff\toStr()
     * @covers \Diff\genDiffFile()
     * @covers \Diff\jsonParser()
     * @covers \Diff\parser()
     * @covers \Diff\getStructure()
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
     * @covers \Diff\getDataFromFile()
     * @covers \Diff\immutableSort()
     * @covers \Diff\toStr()
     * @covers \Diff\genDiffFile()
     * @covers \Diff\jsonParser()
     * @covers \Diff\parser()
     * @covers \Diff\Formatters\format()
     * @covers \Diff\getStructure()
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
     * @covers \Diff\getDataFromFile()
     * @covers \Diff\immutableSort()
     * @covers \Diff\toStr()
     * @covers \Diff\genDiffFile()
     * @covers \Diff\jsonParser()
     * @covers \Diff\parser()
     * @covers \Diff\Formatters\format()
     * @covers \Diff\Formatters\stylish()
     * @covers \Diff\Formatters\iter()
     * @covers \Diff\getStructure()
     * @covers \Diff\genDiff()
     */
    public function test_nested_diff()
    {
        $path1 = __DIR__ . '/fixtures/nested_file1.json';
        $path2 = __DIR__ .'/../'.'tests/fixtures/nested_file2.json';
        $result = genDiffFile($path1, $path2);
        $formatted = format($result, 'stylish');

        $expected = file_get_contents(__DIR__ .'/../'.'tests/fixtures/nested_diff_result');
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
        $expected = file_get_contents(__DIR__ .'/../'.'tests/fixtures/nested_diff_result_2');
        $this->assertEquals($expected, $formatted);
        $this->assertEquals($expected,genDiff($path1,$path2,'stylish'));
    }

    /**
     * @covers \Diff\Formatters\format()
     */
    public function test_formatter()
    {
        $this->expectException(\Exception::class);
        format([],'test');
    }

    /**
     * @covers \Diff\node()
     * @covers \Diff\getKey()
     * @covers \Diff\getChild()
     * @covers \Diff\getType()
     * @covers \Diff\getValue()
     * @covers \Diff\getOldValue()
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
        $this->assertEquals($type,\Diff\getType($node));
        $this->assertEquals($child,getChild($node));
    }
    /**
     * @covers \Diff\getDataFromFile()
     * @covers \Diff\immutableSort()
     * @covers \Diff\toStr()
     * @covers \Diff\genDiffFile()
     * @covers \Diff\jsonParser()
     * @covers \Diff\parser()
     * @covers \Diff\Formatters\format()
     * @covers \Diff\Formatters\plain()
     * @covers \Diff\Formatters\isComplexValue()
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
     * @covers \Diff\getDataFromFile()
     * @covers \Diff\immutableSort()
     * @covers \Diff\toStr()
     * @covers \Diff\genDiffFile()
     * @covers \Diff\jsonParser()
     * @covers \Diff\parser()
     * @covers \Diff\Formatters\format()
     * @covers \Diff\Formatters\json()
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
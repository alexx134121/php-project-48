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
use function Differ\Differ\node;
use function Differ\Differ\toStr;

class DiffTest extends TestCase
{
    /**
     * @dataProvider stringTestDataProvider
     */
    public function testToString($val, $excepted)
    {
        $this->assertEquals($excepted, toStr($val));
    }

    public function testDiff()
    {
        $path1 = __DIR__ . '/fixtures/file1.json';
        $path2 = __DIR__ . '/../' . 'tests/fixtures/file2.json';
        $diff = genDiffFile($path1, $path2);
        $result = format($diff);
        $expected = file_get_contents(__DIR__ . '/../' . 'tests/fixtures/diff_result');

        $this->assertEquals($expected, $result);

        $path1 = __DIR__ . '/fixtures/file1.json';
        $path2 = __DIR__ . '/../' . '/tests/fixtures/file2111.json';
        $this->expectException(\Exception::class);
        $result = genDiffFile($path1, $path2);

        $path1 = __DIR__ . '/fixtures/file1.yml';
        $path2 = __DIR__ . '/../' . '/tests/fixtures/file2.yaml';
        $result = genDiffFile($path1, $path2);
        $expected = file_get_contents(__DIR__ . '/../' . 'tests/fixtures/diff_result');

        $this->assertEquals($expected, $result);

        $path1 = __DIR__ . '/fixtures/file1.yml';
        $path2 = '/tests/fixtures/file2111.yaml';
        $this->expectException(\Exception::class);
        $result = genDiffFile($path1, $path2);
        $this->assertEquals($expected, $result);
    }


    public function testNestedStructure()
    {
        $path1 = __DIR__ . '/fixtures/nested_file1.json';
        $path2 = __DIR__ . '/../' . 'tests/fixtures/nested_file2.json';
        $result = genDiffFile($path1, $path2);
        $expected = json_decode(file_get_contents(__DIR__ . '/fixtures/nested_structure_2.json'), true);
        $this->assertEquals($expected, $result);

        $path1 = __DIR__ . '/fixtures/nested_file1.yaml';
        $path2 = __DIR__ . '/../' . 'tests/fixtures/nested_file2.yaml';
        $result = genDiffFile($path1, $path2);
        $this->assertEquals($expected, $result);
    }

    public function testPlanStructure()
    {
        $path1 = __DIR__ . '/fixtures/file1.json';
        $path2 = __DIR__ . '/../tests/fixtures/file2.json';
        $result = genDiffFile($path1, $path2);
        $expected = json_decode(file_get_contents(__DIR__ . '/fixtures/plan_structure_1.json'), true);
        $this->assertEquals($expected, $result);
    }


    public function testFormatPlanStructure()
    {
        $path1 = __DIR__ . '/fixtures/file1.json';
        $path2 = __DIR__ . '/../' . 'tests/fixtures/file2.json';
        $result = genDiffFile($path1, $path2);
        $formatted = format($result, 'stylish');
        $excepted = file_get_contents(__DIR__ . '/fixtures/diff_result');
        $this->assertEquals($excepted, $formatted);
    }

    public function testNestedDiff()
    {
        $path1 = __DIR__ . '/fixtures/nested_file1.json';
        $path2 = __DIR__ . '/../' . 'tests/fixtures/nested_file2.json';
        $result = genDiffFile($path1, $path2);
        $formatted = format($result, 'stylish');
        $expected = trim(file_get_contents(__DIR__ . '/../' . 'tests/fixtures/nested_diff_result'));
        $this->assertEquals($expected, $formatted);

        $path1 = __DIR__ . '/fixtures/nested_file1.yaml';
        $path2 = __DIR__ . '/../' . 'tests/fixtures/nested_file2.yaml';
        $result = genDiffFile($path1, $path2);
        $formatted = format($result, 'stylish');
        $this->assertEquals($expected, $formatted);


        $path1 = __DIR__ . '/fixtures/nested_file2.yaml';
        $path2 = __DIR__ . '/../' . 'tests/fixtures/nested_file1.yaml';
        $result = genDiffFile($path1, $path2);
        $formatted = format($result, 'stylish');
        $expected = trim(file_get_contents(__DIR__ . '/../' . 'tests/fixtures/nested_diff_result_2'));
        $this->assertEquals($expected, $formatted);
        $this->assertEquals($expected, genDiff($path1, $path2, 'stylish'));
    }


    public function testFormatter()
    {
        $this->expectException(\Exception::class);
        format([], 'test');
    }

    public function testNode()
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
        $node = node($val, $key, $child, $type);
        $this->assertEquals($excepted, $node);
        $this->assertEquals($key, getKey($node));
        $this->assertEquals($val, getValue($node));
        $this->assertEquals($oldValue, getOldValue($node));
        $this->assertEquals($type, \Differ\Differ\getType($node));
        $this->assertEquals($child, getChild($node));
    }

    public function testPlainFormatter()
    {
        $path1 = __DIR__ . '/fixtures/nested_file1.json';
        $path2 = __DIR__ . '/fixtures/nested_file2.json';
        $result = genDiffFile($path1, $path2);
        $formatted = format($result, 'plain');
        $expected = file_get_contents(__DIR__ . '/fixtures/diff_plain_format');
        $this->assertEquals($expected, $formatted);
    }

    public function testJsonStructure()
    {
        $path1 = __DIR__ . '/fixtures/nested_file1.json';
        $path2 = __DIR__ . '/fixtures/nested_file2.json';
        $result = genDiffFile($path1, $path2);
        $formatted = format($result, 'json');
        $expected = file_get_contents(__DIR__ . '/fixtures/diff_json_format');
        $this->assertEquals($expected, $formatted);
    }

    public function stringTestDataProvider()
    {
        return [
            [true, 'true'],
            [null, 'null'],
            [4, 4],
            ['test', 'test'],
        ];
    }
}

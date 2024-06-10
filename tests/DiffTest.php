<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Formatters\Formatters\format;
use function Differ\Differ\genDiff;
use function Differ\Differ\toStr;

class DiffTest extends TestCase
{
    public const FIXTURE_PATH = __DIR__ . '/fixtures/';

    /**
     * @dataProvider stringTestDataProvider
     */
    public function testToString($val, $excepted)
    {
        $this->assertEquals($excepted, toStr($val));
    }


    /**
     * @dataProvider nestedDiffDataProvider
     */
    public function testNestedDiff($path1, $path2, $expectedPath)
    {
        $formatted = genDiff(self::FIXTURE_PATH . $path1, self::FIXTURE_PATH . $path2, 'stylish');
        $expected = trim(file_get_contents(self::FIXTURE_PATH . $expectedPath));
        $this->assertEquals($expected, $formatted);
    }

    public function testFormatterNotAvailable()
    {
        $this->expectException(\Exception::class);
        format([], 'test');
    }

    /**
     * @dataProvider formatterDataProvider
     */
    public function testFormatter($path1, $path2, $expectedPath, $format)
    {
        $formatted = genDiff(self::FIXTURE_PATH . $path1, self::FIXTURE_PATH . $path2, $format);
        $expected = trim(file_get_contents(self::FIXTURE_PATH . $expectedPath));
        $this->assertEquals($expected, $formatted);
    }


    public function formatterDataProvider()
    {
        return [
            ['nested_file1.json', 'nested_file2.json', 'diff_plain_format', 'plain'],
            ['nested_file1.json', 'nested_file2.json', 'diff_json_format', 'json'],
            ['nested_file1.json', 'nested_file2.json', 'nested_diff_result', 'stylish'],
        ];
    }

    public function nestedDiffDataProvider()
    {
        return [
            [
                'nested_file1.json',
                'nested_file2.json',
                'nested_diff_result',
            ],
            [
                'nested_file1.yaml',
                'nested_file2.yaml',
                'nested_diff_result'
            ],
            [
                'nested_file2.yaml',
                'nested_file1.yaml',
                'nested_diff_result_2',
            ],
        ];
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

<?php

namespace Differ\Tests;

use Hexlet\Tests\BaseTestCase;
use Exception;

use function Differ\Formatters\Formatters\format;
use function Differ\Differ\genDiff;

class FormatterTest extends BaseTestCase
{
    public function testFormatterNotAvailable()
    {
        $this->expectException(Exception::class);
        format([], 'test');
    }

    /**
     * @dataProvider formatterDataProvider
     */
    public function testFormatter($path1, $path2, $expectedPath, $format)
    {
        $formatted = genDiff($this->getFullPathFixtures($path1), $this->getFullPathFixtures($path2), $format);
        $expected = trim(file_get_contents($this->getFullPathFixtures($expectedPath)));
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
}

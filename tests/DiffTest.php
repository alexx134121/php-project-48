<?php

namespace Differ\Tests;

use Hexlet\Tests\BaseTestCase;

use function Differ\Differ\makeTree;
use function Differ\Differ\toStr;
use function Differ\Parsers\Parsers\parserData;

class DiffTest extends BaseTestCase
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
        $data1 = parserData($this->getFullPathFixtures($path1));
        $data2 = parserData($this->getFullPathFixtures($path2));
        $diffTree = makeTree($data1, $data2);
        $expected = json_decode(file_get_contents($this->getFullPathFixtures($expectedPath)), true);
        $this->assertEquals($expected, $diffTree);
    }

    public function nestedDiffDataProvider()
    {
        return [
            [
                'nested_file1.json',
                'nested_file2.json',
                'nested_structure_2.json',
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

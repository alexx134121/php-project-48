<?php

namespace Differ\Tests;

use Hexlet\Tests\BaseTestCase;
use Exception;

use function Differ\Parsers\Parsers\parserData;

class ParserTest extends BaseTestCase
{
    public function testFormatFile()
    {
        $path = $this->getFullPathFixtures('file2.txt');
        $this->expectException(Exception::class);
        $data = parserData($path);
    }

    /**
     * @dataProvider parserDataProvider
     */
    public function testParser($path)
    {
        $this->assertIsArray(parserData($this->getFullPathFixtures($path)));
    }

    public function parserDataProvider()
    {
        return [
            [
                'nested_file1.json',
                'nested_file2.yaml',
                'file1.yml',
            ],
        ];
    }
}

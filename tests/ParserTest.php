<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;
use Exception;

use function Differ\Parsers\Parsers\parserData;

class ParserTest extends TestCase
{
    public const FIXTURE_PATH = __DIR__ . '/fixtures/';

    public function testFormatFile()
    {
        $path = self::FIXTURE_PATH . 'file2.txt';
        $this->expectException(Exception::class);
        $data = parserData($path);
    }

    /**
     * @dataProvider parserDataProvider
     */
    public function testParser($path)
    {
        $this->assertIsArray(parserData(self::FIXTURE_PATH . $path));
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

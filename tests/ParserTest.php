<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\getDataFromFile;
use function Differ\jsonParser;
use function Differ\parser;
use function Differ\ymlParser;

class ParserTest extends TestCase
{
    /**
     * @covers \Differ\getDataFromFile()
     */
    public function testFileNotExists()
    {
        $errorPath = 'fixtures/file2111.json';
        $this->expectException(\Exception::class);
        $result = getDataFromFile($errorPath);
        $this->assertEquals('Не найден путь к файлу fixtures/file2111.json', $result);
    }

    /**
     * @covers \Differ\getDataFromFile()
     */
    public function testReadFileAbsolute()
    {
        $path = __DIR__ . '/fixtures/file2.json';
        $result = getDataFromFile($path);
        $this->assertNotEquals('Не найден путь к файлу fixtures/file2111.json', $result);
    }

    /**
     * @covers \Differ\getDataFromFile()
     */
    public function testReadFile()
    {
        $path = 'tests/fixtures/file2.json';
        $result = getDataFromFile($path);
        $this->assertNotEquals('Не найден путь к файлу fixtures/file2111.json', $result);
    }

    /**
     * @covers \Differ\jsonParser()
     */
    public function testJSONParser()
    {
        $excepted = array(
            'timeout' => 20,
            'verbose' => true,
            'host' => 'hexlet.io',
        );
        $path = 'tests/fixtures/file2.json';
        $content = file_get_contents($path);
        $data = jsonParser($content);
        $this->assertEquals($excepted, $data);
    }

    /**
     * @covers \Differ\ymlParser()
     */
    public function testYAMLParser()
    {
        $excepted = array(
            'timeout' => 20,
            'verbose' => true,
            'host' => 'hexlet.io',
        );
        $path = 'tests/fixtures/file2.json';
        $content = file_get_contents($path);
        $data = ymlParser($content);
        $this->assertEquals($excepted, $data);
    }

    /**
     * @covers \Differ\getDataFromFile()
     * @covers \Differ\jsonParser()
     * @covers \Differ\ymlParser()
     * @covers \Differ\parser()
     */
    public function testParser()
    {
        $excepted = array(
            'timeout' => 20,
            'verbose' => true,
            'host' => 'hexlet.io',
        );
        $path = 'tests/fixtures/file2.json';
        $data = parser($path);
        $this->assertEquals($excepted, $data);

        $path = 'tests/fixtures/file2.yaml';
        $data = parser($path);
        $this->assertEquals($excepted, $data);


        $path = 'tests/fixtures/file2.txt';
        $this->expectException(\Exception::class);
        $data = parser($path);
    }
}

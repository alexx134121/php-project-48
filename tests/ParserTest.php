<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;
use function Differ\Differ\genDiff;
use function Differ\getDataFromFile;
use function Differ\Differ\immutableSort;
use function Differ\jsonParser;
use function Differ\parser;
use function Differ\Differ\toStr;
use function Differ\ymlParser;

class ParserTest extends TestCase
{

    /**
     * @covers \Differ\getDataFromFile()
     */
    public function test_file_not_exists()
    {
        $errorPath = 'fixtures/file2111.json';
        $this->expectException(\Exception::class);
        $result = getDataFromFile($errorPath);
        $this->assertEquals('Не найден путь к файлу fixtures/file2111.json', $result);
    }

    /**
     * @covers \Differ\getDataFromFile()
     */
    public function test_read_file_absolute()
    {
        $path = __DIR__ . '/fixtures/file2.json';
        $result = getDataFromFile($path);
        $this->assertNotEquals('Не найден путь к файлу fixtures/file2111.json', $result);
    }

    /**
     * @covers \Differ\getDataFromFile()
     */
    public function test_read_file()
    {
        $path = 'tests/fixtures/file2.json';
        $result = getDataFromFile($path);
        $this->assertNotEquals('Не найден путь к файлу fixtures/file2111.json', $result);
    }

    /**
     * @covers \Differ\jsonParser()
     */
    public function test_JSON_parser()
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
    public function test_YAML_parser()
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
    public function test_parser()
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
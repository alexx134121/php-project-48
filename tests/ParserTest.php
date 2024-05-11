<?php

namespace Diff\Tests;

use PHPUnit\Framework\TestCase;
use function Diff\genDiff;
use function Diff\getDataFromFile;
use function Diff\immutableSort;
use function Diff\jsonParser;
use function Diff\parser;
use function Diff\toStr;
use function Diff\ymlParser;

class ParserTest extends TestCase
{

    /**
     * @covers \Diff\getDataFromFile()
     */
    public function test_file_not_exists()
    {
        $errorPath = 'fixtures/file2111.json';
        $this->expectException(\Exception::class);
        $result = getDataFromFile($errorPath);
        $this->assertEquals('Не найден путь к файлу fixtures/file2111.json', $result);
    }

    /**
     * @covers \Diff\getDataFromFile()
     */
    public function test_read_file_absolute()
    {
        $path = __DIR__ . '/fixtures/file2.json';
        $result = getDataFromFile($path);
        $this->assertNotEquals('Не найден путь к файлу fixtures/file2111.json', $result);
    }

    /**
     * @covers \Diff\getDataFromFile()
     */
    public function test_read_file()
    {
        $path = 'tests/fixtures/file2.json';
        $result = getDataFromFile($path);
        $this->assertNotEquals('Не найден путь к файлу fixtures/file2111.json', $result);
    }

    /**
     * @covers \Diff\jsonParser()
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
     * @covers \Diff\ymlParser()
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
     * @covers \Diff\getDataFromFile()
     * @covers \Diff\jsonParser()
     * @covers \Diff\ymlParser()
     * @covers \Diff\parser()
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
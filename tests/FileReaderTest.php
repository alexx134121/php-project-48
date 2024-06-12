<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;
use Exception;

use function Differ\FileReader\getDataFromFile;

class FileReaderTest extends TestCase
{
    protected const FIXTURE_PATH = __DIR__ . '/fixtures/';

    public function testFileNotExists()
    {
        $errorPath = 'fixtures/file2111.json';
        $this->expectException(Exception::class);
        $result = getDataFromFile($errorPath);
    }

    /**
     * @dataProvider pathFilesDataProvider
     */
    public function testReadFiles($path)
    {
        $result = getDataFromFile($path);
        $this->assertNotEmpty($result);
    }

    public function pathFilesDataProvider()
    {
        return [
            [self::FIXTURE_PATH . 'file2.json'],
            [self::FIXTURE_PATH . 'file2.json'],
        ];
    }
}

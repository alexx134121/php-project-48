<?php

namespace Differ\Tests;

use Hexlet\Tests\BaseTestCase;
use Exception;

use function Differ\FileReader\getDataFromFile;

class FileReaderTest extends BaseTestCase
{
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
        $result = getDataFromFile($this->getFullPathFixtures($path));
        $this->assertNotEmpty($result);
    }

    public function pathFilesDataProvider()
    {
        return [
            ['file2.json'],
            ['file2.yaml'],
        ];
    }
}

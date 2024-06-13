<?php

namespace Hexlet\Tests;

class BaseTestCase extends \PHPUnit\Framework\TestCase
{
    public function getFullPathFixtures(string $fileName): string
    {
        return __DIR__ . '/fixtures/' . $fileName;
    }
}

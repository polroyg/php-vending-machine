<?php

namespace Tests\Infrastructure;

use App\Infrastructure\JsonStorage;
use PHPUnit\Framework\TestCase;

class JsonStorageTest extends TestCase
{
    public function testCreateFile()
    {
        $filePath = "/tmp/sample.test.json";
        $jsonStorage = new JsonStorage($filePath);
        $this->assertInstanceOf(JsonStorage::class, $jsonStorage);
        $this->assertFileExists($filePath);
    }

    public function testCreateFileFail()
    {
        $filePath = "/root/sample.test.json";
        $this->expectException(\InvalidArgumentException::class);
        $jsonStorage = new JsonStorage($filePath);
        $this->assertFileDoesNotExist($filePath);
    }

    public function testSaveAndLoadData()
    {
        $filePath = "/tmp/sample.test.json";
        $jsonStorage = new JsonStorage($filePath);
        $data = ["id1" => ["name" => "Test", "value" => 10.20]];
        $jsonStorage->save($data);
        $loadedData = $jsonStorage->load();
        $this->assertEquals($data, $loadedData);
    }
}

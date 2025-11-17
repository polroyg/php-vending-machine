<?php

namespace App\Infrastructure;

class JsonStorage
{
    private string $filePath;

    public function __construct($filePath)
    {
        if (!file_exists($filePath)) {
            $dir = dirname($filePath);
            if (!is_dir($dir) && !mkdir($dir, 0775, true)) {
                throw new \InvalidArgumentException("Cannot access path " . $dir);
            }
            // Create empty file
            if (false === @touch($filePath)) {
                throw new \InvalidArgumentException("Cannot create file " . $filePath);
            }
        }
        if (!is_writable($filePath) && !chmod($filePath, 0664)) {
            throw new \InvalidArgumentException("No permissions for the file " . $filePath);
        }
        $this->filePath = $filePath;
    }

    public function load(): array
    {
        $content = file_get_contents($this->filePath);
        return json_decode($content, true) ?? [];
    }

    public function save(array $data): void
    {
        file_put_contents($this->filePath, json_encode($data));
    }
}

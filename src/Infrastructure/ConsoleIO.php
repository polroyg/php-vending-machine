<?php

namespace App\Infrastructure;

class ConsoleIO
{
    public function readInput($input): array
    {
        $options = trim($input);
        if (empty($options)) {
            return [];
        }
        $options_list = explode(",", $options);
        $options_cleaned = array_map(fn($value): string => trim($value), $options_list);
        return $options_cleaned;
    }


    public function showMainMenu(): void
    {
        $this->showMessage("SELECT ACCESS MODE:");
        $this->showMessage("------------------");
        $this->showMessage(" 1. Customer mode");
        $this->showMessage(" 2. Service mode");
        $this->showMessage(" 0. Exit");
        $this->showMessage("");
    }

    public function getCustomerMenu(): void
    {
        $this->showMessage("SELECT AN OPTION:");
        $this->showMessage("----------------");
        $this->showMessage(" 1. Insert Coin");
        $this->showMessage(" 2. Get Item");
        $this->showMessage(" 3. Return Coins");
        $this->showMessage(" 4. View amount inserted");
        $this->showMessage(" 0. Exit");
        $this->showMessage("");
    }
    public function showServiceMenu(): void
    {
        $this->showMessage("SELECT AN OPTION:");
        $this->showMessage("----------------");
        $this->showMessage("1. Sales report");
        $this->showMessage("2. Add change money");
        $this->showMessage("3. View inventory");
        $this->showMessage("4. Restock");
        $this->showMessage("0. Exit service mode");
        $this->showMessage("");
    }
    public function showMessage(string $message): void
    {
        print_r($message . PHP_EOL);
    }
    public function showMessageInline(string $message): void
    {
        print_r($message);
    }
}

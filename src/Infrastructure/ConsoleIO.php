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
        $this->showMessage("Select an option");
        $this->showMessage("1. Customer mode");
        $this->showMessage("2. Service mode");
        $this->showMessage("0. Exit");
    }

    public function showCustomerMenu(): void
    {
        $this->showMessage("Select an action:");
        $this->showMessage("1. Insert Coin");
        $this->showMessage("2. Get Item");
        $this->showMessage("3. Return Coins");
        $this->showMessage("4. Service Mode");
        $this->showMessage("0. Exit");
    }
    public function showServiceMenu(): void
    {
        $this->showMessage("Select a service action:");
        $this->showMessage("1. Sales report");
        $this->showMessage("2. Add change money");
        $this->showMessage("3. View inventory");
        $this->showMessage("4. Restock");
        $this->showMessage("0. Exit service mode");
    }
    public function showMessage(string $message): void
    {
        print_r($message . PHP_EOL);
    }
}

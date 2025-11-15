<?php

namespace App\Infrastructure;

class ConsoleIO
{
    //Leer parametros
    //Mostrar menu
    //Validar opciones de entrada
    private array $ACCEPTED_ACTIONS = ["SERVICE", "MENU", "EXIT"];

    public function readInput(): array
    {
        $options = trim(fgets(STDIN));
        $options_list = explode(",", $options);
        $options_cleaned = array_map(fn($value): string => trim($value), $options_list);
        return $options_cleaned;
    }

    public function validateAction($action): bool
    {
        if (!in_array($action, $this->ACCEPTED_ACTIONS) and !str_starts_with($action, "GET-")) {
            return false;
        }
        return true;
    }

    public function showMainMenu(): void
    {
        $this->showMessage("Select an action or insert a command:");
        $this->showMessage("Actions available: MENU, EXIT, SERVICE");
        $this->showMessage("Command availables: show instructions");
    }

    public function showClientMenu(): void
    {
        $this->showMessage("Select an action:");
        $this->showMessage("1. Insert Coin");
        $this->showMessage("2. Get Item");
        $this->showMessage("3. Return Coins");
        $this->showMessage("4. Service Mode");
    }
    public function showMessage(string $message): void
    {
        print_r($message . PHP_EOL);
    }
}

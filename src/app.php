#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Infrastructure\ConsoleIO;

set_time_limit(0);
try {
    print_r("Starting Vending Machine Application..." . PHP_EOL);
    $consoleIO = new ConsoleIO();
    print_r("Starting Vending Machine Application..." . PHP_EOL);

    $consoleIO->showMessage("Welcome to Vending Machine Application");

    $consoleIO->showMainMenu();
    $read_input = $consoleIO->readInput();

    $num_inputs = count($read_input);

    if ($num_inputs === 0) {
        $consoleIO->showMessage("No action selected. Exiting...");
        exit(0);
    } elseif ($num_inputs > 1) {
        $consoleIO->showMessage("Command inserted: " . implode(", ", $read_input));
        exit(0);
    } else {
        $action = $read_input[0];
        if (!$consoleIO->validateAction($action)) {
            $consoleIO->showMessage("Invalid action selected. Exiting...");
            exit(0);
        }
    }
} catch (\Throwable $th) {
    //throw $th;
    print_r("An error has occurred: " . $th->getMessage() . PHP_EOL);
}

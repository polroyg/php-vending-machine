#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Infrastructure\ConsoleIO;

set_time_limit(0);
try {
    $consoleIO = new ConsoleIO();
    $consoleIO->showMessage("Welcome to Vending Machine Application");

    $consoleIO->showMainMenu();
    $read_input = $consoleIO->readInput(fgets(STDIN));

    $num_inputs = count($read_input);

    if ($num_inputs === 0) {
        $consoleIO->showMessage("No action selected. Exiting...");
        exit(0);
    } elseif ($num_inputs > 1) {
        $consoleIO->showMessage("Command inserted: " . implode(", ", $read_input));
        exit(0);
    } else {
        $action = $read_input[0];
        $exit = $action === "EXIT";
        while (!$exit) {
            if (!$consoleIO->validateAction($action)) {
                $consoleIO->showMessage("Invalid action selected. Exiting...");
                exit(0);
            }

            if ($action === "SERVICE") {
                processServiceActions($consoleIO);
            } else {
                processCustomerActions($consoleIO);
            }
        }
        $consoleIO->showMessage("Closing the app...");
    }
} catch (\Throwable $th) {
    //throw $th;
    print_r("An error has occurred: " . $th->getMessage() . PHP_EOL);
}


function processServiceActions($consoleIO)
{
    $consoleIO->showServiceMenu();
}

function processCustomerActions($consoleIO)
{
    $consoleIO->showCustomerMenu()
}

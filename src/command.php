#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Infrastructure\ConsoleIO;

set_time_limit(0);
try {
    $consoleIO = new ConsoleIO();
    $consoleIO->showMessage("Welcome to Vending Machine Command");

    if ($argc != 1) {
        throw new \InvalidArgumentException("Expected exactly one parameter");
    }

    $read_input = $consoleIO->readInput($argv[0]);

    $num_inputs = count($read_input);

    if ($num_inputs === 0) {
        $consoleIO->showMessage("No action selected. Exiting...");
        exit(0);
    } elseif ($num_inputs > 1) {
        $consoleIO->showMessage("Command inserted: " . implode(", ", $read_input));
        exit(0);
    }
} catch (\Throwable $th) {
    //throw $th;
    print_r("An error has occurred: " . $th->getMessage() . PHP_EOL);
}

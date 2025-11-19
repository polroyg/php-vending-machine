#!/usr/bin/env php
<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '0');

require __DIR__ . '/../vendor/autoload.php';

use App\Application;

set_time_limit(0);
try {
    $app = new Application();
    $app->run();
} catch (\Throwable $th) {
    //throw $th;
    print_r("An error has occurred: " . $th->getMessage() . PHP_EOL);
}

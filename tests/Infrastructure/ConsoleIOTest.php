<?php

namespace Tests\Infrastructure;

use App\Infrastructure\ConsoleIO;
use PHPUnit\Framework\TestCase;

class ConsoleIOTest extends TestCase
{
    public function testReadInputEmpty()
    {
        $consoleIO = new ConsoleIO();
        $parameters = $consoleIO->readInput("");
        $this->assertEmpty($parameters);
    }

    public function testReadInput()
    {
        $consoleIO = new ConsoleIO();
        $parameters = $consoleIO->readInput("param1, param2, param3");
        $this->assertTrue(sizeof($parameters) == 3);
    }
}

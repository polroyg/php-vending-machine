<?php

namespace Tests\Domain;

use App\Domain\Coin;
use PHPUnit\Framework\TestCase;

class CoinTest extends TestCase
{
    public function testInvalidCoin()
    {
        try {
            $coin = new Coin(0.4);
        } catch (\Throwable $th) {
            $this->assertTrue($th instanceof \InvalidArgumentException);
        }
        $this->assertFalse(false);
    }

    public function testValidCoin()
    {
        try {
            $validValues = [0.05, 0.10, 0.25, 1.00];
            foreach ($validValues as $value) {
                $coin = new Coin($value);
            }
        } catch (\Throwable $th) {
            $this->assertFalse($th instanceof \InvalidArgumentException);
        }
        $this->assertTrue(true);
    }

    public function testInvalidQuantity()
    {
        try {
            $coin = new Coin(0.5, -10);
        } catch (\Throwable $th) {
            $this->assertTrue($th instanceof \InvalidArgumentException);
        }
        $this->assertFalse(false);
    }

    public function testEquals()
    {
        $coin = new Coin(0.05, 5);
        $coin2 = new Coin(0.05, 15);
        $this->assertTrue($coin->equals($coin2));
    }

    public function testNotEquals()
    {
        $coin = new Coin(0.05, 5);
        $coin2 = new Coin(0.10, 15);
        $this->assertFalse($coin->equals($coin2));
    }
}

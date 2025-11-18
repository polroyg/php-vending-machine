<?php

namespace Tests\Domain;

use App\Domain\Coin;
use App\Domain\Transaction;
use PHPUnit\Framework\TestCase;

class TransactionTest extends TestCase
{

    public function testCreateEmptyTransaction()
    {
        $transaction = new Transaction();

        $this->assertNull($transaction->getItem());
        $this->assertEquals(0, $transaction->getBalance());
        $this->assertEquals(0, count($transaction->getCoins()));
    }

    public function testAddCoinsAndValidateBalance()
    {
        try {
            $coin1 = new Coin(0.25);
            $coin2 = new Coin(0.10);
            $coin3 = new Coin(1);
            $transaction = new Transaction([$coin1, $coin2, $coin3]);

            $expected_balance = 0.25 + 0.10 + 1;

            $this->assertEquals($expected_balance, $transaction->getBalance());
        } catch (\Throwable $th) {
            $this->assertTrue(false);
        }
    }
}

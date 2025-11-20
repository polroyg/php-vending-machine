<?php

namespace Tests\Domain;

use App\Domain\CashBox;
use App\Domain\CashBoxItem;
use App\Domain\Coin;
use PHPUnit\Framework\TestCase;

class CashBoxTest extends TestCase
{
    private function initCashBox(): CashBox
    {
        $cashBoxItem05 = new CashBoxItem(new Coin(0.05), 10);
        $cashBoxItem10 = new CashBoxItem(new Coin(0.10), 5);
        $cashBoxItem25 = new CashBoxItem(new Coin(0.25), 4);
        $cashBoxItem1 = new CashBoxItem(new Coin(1), 3);
        $cashBox = new CashBox([$cashBoxItem05, $cashBoxItem10, $cashBoxItem25, $cashBoxItem1]);
        return $cashBox;
    }

    public function testAddAndTakeCoin()
    {
        $cashBox = $this->initCashBox();
        $initialTotal = $cashBox->getTotalAmount();
        $coin = new Coin(0.10);
        $cashBox->addCoin($coin);
        $currentTotal = $cashBox->getTotalAmount();
        $this->assertEquals(($initialTotal + $coin->getValue()), $currentTotal);

        $cashBox->takeCoin($coin);
        $currentTotalAfterTake = $cashBox->getTotalAmount();
        $this->assertEquals($initialTotal, $currentTotalAfterTake);
    }

    public function testCheckChangeAvailable()
    {
        $cashBox = $this->initCashBox();
        $this->assertTrue($cashBox->checkChangeAvailable(0.30, [new Coin(0.10)]));
        $this->assertFalse($cashBox->checkChangeAvailable(6, []));
    }

    public function testReturnChange()
    {
        $cashBox = $this->initCashBox();
        $returnCoins = $cashBox->calculateChange(0.30);

        $expectedReturn = [new Coin(0.25), new Coin(0.05)];
        $this->assertIsArray($returnCoins);
        $this->assertEquals($expectedReturn, $returnCoins);
    }

    public function testReturnChangeExact()
    {
        $cashBoxItem1 = new CashBoxItem(new Coin(1), 3);
        $cashBoxItem2 = new CashBoxItem(new Coin(0.10), 2);
        $cashBox = new CashBox([$cashBoxItem1, $cashBoxItem2]);
        $returnCoins = $cashBox->calculateChange(3.20);

        $expectedReturn = [new Coin(1), new Coin(1), new Coin(1), new Coin(0.10), new Coin(0.10)];
        $this->assertIsArray($returnCoins);
        $this->assertEquals($expectedReturn, $returnCoins);
    }
}

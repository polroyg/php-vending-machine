<?php

namespace Tests\Domain;

use App\Domain\Coin;
use App\Domain\CashBoxItem;
use PHPUnit\Framework\TestCase;

class CashBoxItemTest extends TestCase
{
    public function testEquals()
    {
        $chashBoxItem1 = new CashBoxItem(new Coin(0.10), 5);
        $chashBoxItem2 = new CashBoxItem(new Coin(0.10), 10);
        $this->assertTrue($chashBoxItem1->equals($chashBoxItem2));
    }

    public function testNotEquals()
    {
        $chashBoxItem1 = new CashBoxItem(new Coin(0.10), 5);
        $chashBoxItem2 = new CashBoxItem(new Coin(0.05), 10);
        $this->assertFalse($chashBoxItem1->equals($chashBoxItem2));
    }
}

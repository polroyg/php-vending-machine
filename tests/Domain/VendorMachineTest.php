<?php

namespace Tests\Domain;

use App\Domain\CashBoxItem;
use App\Domain\Coin;
use App\Domain\Item;
use App\Domain\Transaction;
use PHPUnit\Framework\TestCase;
use App\Domain\VendingMachine;
use App\Infrastructure\Repositories\ItemJsonRepository;
use App\Infrastructure\Repositories\CashBoxItemJsonRepository;

class VendorMachineTest extends TestCase
{
    private $itemRepository;
    private $cashBoxRepository;
    private VendingMachine $vendingMachine;

    protected function setUp(): void
    {
        $this->itemRepository = $this->createMock(ItemJsonRepository::class);
        $this->itemRepository
            ->method('findAll')
            ->willReturn([
                new Item("JUICE", "Juice", 1.00, 10),
                new Item("SODA", "Soda", 1.50, 5),
                new Item("WATER", "Water", 0.65, 20)
            ]);
        $this->itemRepository
            ->method('findByKey')
            ->willReturnCallback(function ($key) {
                $items = [
                    "JUICE" => new Item("JUICE", "Juice", 1.00, 10),
                    "SODA" => new Item("SODA", "Soda", 1.50, 5),
                    "WATER" => new Item("WATER", "Water", 0.65, 20)
                ];
                return $items[$key] ?? null;
            });
        $this->cashBoxRepository = $this->createMock(CashBoxItemJsonRepository::class);
        $this->cashBoxRepository
            ->method('findAll')
            ->willReturn([
                new CashBoxItem(new Coin(1.00), 5),
                new CashBoxItem(new Coin(0.25), 2),
                new CashBoxItem(new Coin(0.10), 6),
                new CashBoxItem(new Coin(0.05), 1)
            ]);


        $this->vendingMachine = new VendingMachine(
            $this->itemRepository,
            $this->cashBoxRepository
        );
    }

    public function testStartTransaction(): void
    {
        $this->vendingMachine->startTransaction();
        $transaction = $this->vendingMachine->getCurrentTransaction();
        $this->assertInstanceOf(Transaction::class, $transaction);
    }

    public function testStartMultiplesTransactions(): void
    {
        $this->vendingMachine->startTransaction();
        $this->expectException(\Exception::class);
        $this->vendingMachine->startTransaction();
    }

    public function testAddCoinToTransaction(): void
    {
        $this->vendingMachine->startTransaction();
        $coin = new Coin(0.25);
        $this->vendingMachine->addCoinToTransaction($coin);
        $transactionBalance = $this->vendingMachine->getCurrentTranssactionBalance();
        $this->assertEquals(0.25, $transactionBalance);
    }

    public function testAddInvalidCoinAndRefundToTransaction(): void
    {
        try {
            $this->vendingMachine->startTransaction();
            $coin = new Coin(2);
            $this->vendingMachine->addCoinToTransaction($coin);
        } catch (\Exception $th) {
            $returnCoins = $this->vendingMachine->refundTransaction();
            $this->assertCount(1, $returnCoins);
        }
    }

    public function testBuyItemSuccessfully(): void
    {
        $this->vendingMachine->startTransaction();
        $this->vendingMachine->addCoinToTransaction(new Coin(1.00));
        $result = $this->vendingMachine->buyItem("JUICE", 1);
        $this->assertArrayHasKey('item', $result);
        $this->assertEquals("JUICE", $result['item']->getKey());
        $this->assertCount(0, $result['change']);
    }

    public function testBuyItemWithChange(): void
    {
        $this->vendingMachine->startTransaction();
        $this->vendingMachine->addCoinToTransaction(new Coin(1.00));
        $this->vendingMachine->addCoinToTransaction(new Coin(0.25));
        $this->vendingMachine->addCoinToTransaction(new Coin(0.25));
        $result = $this->vendingMachine->buyItem("JUICE", 1);
        $this->assertArrayHasKey('item', $result);
        $this->assertEquals("JUICE", $result['item']->getKey());
        $this->assertCount(2, $result['change']);
        $this->assertEquals(0.50, array_reduce($result['change'], fn($sum, $coin) => $sum + $coin->getValue(), 0));
    }

    public function testBuyItemWithInsufficientFunds(): void
    {
        $this->vendingMachine->startTransaction();
        $this->vendingMachine->addCoinToTransaction(new Coin(0.25));
        $this->vendingMachine->addCoinToTransaction(new Coin(0.25));
        $this->expectException(\Exception::class);
        $this->vendingMachine->buyItem("JUICE", 1);
    }
}

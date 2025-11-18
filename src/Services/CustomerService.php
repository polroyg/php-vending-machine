<?php

namespace App\Services;

use App\Domain\VendingMachine;
use App\Domain\Coin;
use App\Domain\Item;
use App\Domain\Transaction;
use App\Infrastructure\Repositories\ItemJsonRepository;
use App\Infrastructure\Repositories\CashBoxItemJsonRepository;

class CustomerService
{
    private VendingMachine $vendingMachine;

    public function __construct(
        ItemJsonRepository $itemRepository,
        CashBoxItemJsonRepository $CashBoxItemJsonRepository
    ) {
        $this->vendingMachine = new VendingMachine($itemRepository, $CashBoxItemJsonRepository);
        $this->vendingMachine->startTransaction();
    }

    public function addCoin(float $value): void
    {
        $coin = new Coin($value);
        $this->vendingMachine->addCoinToTransaction($coin);
    }

    public function buyItem(string $itemKey, int $quantity = 1): array
    {
        return $this->vendingMachine->buyItem($itemKey, $quantity);
    }

    public function cancelPurchase(): array
    {
        return $this->vendingMachine->refundTransaction();
    }
}

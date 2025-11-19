<?php

namespace App\Services;

use App\Domain\VendingMachine;
use App\Domain\Coin;
use App\Domain\Item;
use App\Domain\Transaction;
use App\Infrastructure\Repositories\ItemJsonRepository;
use App\Infrastructure\Repositories\CashBoxItemJsonRepository;

class MaintenanceService
{
    private VendingMachine $vendingMachine;

    public function __construct(
        ItemJsonRepository $itemRepository,
        CashBoxItemJsonRepository $cashBoxItemJsonRepository
    ) {
        $this->vendingMachine = new VendingMachine($itemRepository, $cashBoxItemJsonRepository);
    }

    public function viewAvailableItems(): array
    {
        return $this->vendingMachine->getAvailableItems();
    }

    public function availableChange(): array
    {
        return $this->vendingMachine->getAcceptedCoins();
    }

    public function getCurrentTransactionBalance(): float
    {
        return $this->vendingMachine->getCurrentTranssactionBalance();
    }

    public function restockItem(string $itemKey, int $quantity): void
    {
        $this->vendingMachine->restockItem($itemKey, $quantity);
    }
}

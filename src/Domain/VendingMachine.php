<?php

namespace App\Domain;

use App\Infrastructure\Repositories\ItemJsonRepository;
use App\Infrastructure\Repositories\CashBoxItemJsonRepository;

class VendingMachine
{
    private ItemJsonRepository $itemRepository;
    private CashBoxItemJsonRepository $cashBoxItemJsonRepository;
    private CashBox $cashBox;
    private ?Transaction $transaction;
    private const VALID_COIN_VALUES = [0.05, 0.10, 0.25, 1.00];


    public function __construct(
        ItemJsonRepository $itemRepository,
        CashBoxItemJsonRepository $cashBoxItemJsonRepository
    ) {
        $this->itemRepository = $itemRepository;
        $this->cashBoxItemJsonRepository = $cashBoxItemJsonRepository;
        $this->cashBox = new CashBox($cashBoxItemJsonRepository->findAll());
        $this->transaction = null;
    }

    public function startTransaction(): void
    {
        if ($this->transaction !== null) {
            throw new \Exception("Transaction already started");
        }
        $this->transaction = new Transaction();
    }

    public function getCurrentTransaction(): ?Transaction
    {
        return $this->transaction;
    }

    public function getCurrentTranssactionBalance(): float
    {
        if ($this->transaction === null) {
            throw new \Exception("No active transaction");
        }
        return $this->transaction->getBalance();
    }

    public function closeTransaction(): void
    {
        if ($this->transaction === null) {
            throw new \Exception("No active transaction to close");
        }
        $this->transaction = null;
    }

    public function addCoinToTransaction(Coin $coin): void
    {
        if ($this->isCoinAccepted($coin)) {
            $this->transaction->addCoin($coin);
        } else {
            $this->transaction->addInvalidCoin($coin);
            throw new \Exception("Invalid coin value: " . $coin->getValue());
            //TODO: crear una excepción para capturar y mostrar mensaje
        }
    }

    public function refundTransaction(): array
    {
        if ($this->transaction === null) {
            throw new \Exception("No active transaction to refund");
        }
        $coins_return = array_merge($this->transaction->getCoins(), $this->transaction->getInvalidCoins());
        $this->transaction->clearCoins();
        return $coins_return;
    }

    public function rollbackTransaction(): void
    {
        if ($this->transaction !== null) {
            $item = $this->transaction->getItem();
            if ($item !== null) {
                $item->increaseQuantity($this->transaction->getQuantity());
                $this->itemRepository->update($item);
            }

            if ($this->transaction->getCoins() !== null) {
                $this->cashBox->takeCoins($this->transaction->getCoins());
                $this->cashBoxItemJsonRepository->updateAll($this->cashBox->getItems());
            }
        }
    }

    public function buyItem(string $itemKey, int $quantity = 1): Item
    {
        try {
            $item = $this->itemRepository->findByKey($itemKey);
            if (!$item || $item->getQuantity() < $quantity) {
                throw new \Exception("Not enough stock for item: " . $itemKey);
            }
            if ($item->getPrice() * $quantity > $this->transaction->getBalance()) {
                throw new \Exception("Not enough balance for item: " . $itemKey);
            }
            $checkChange = $this->cashBox->checkChangeAvailable(
                $item->getPrice() * $quantity,
                $this->transaction->getCoins()
            );
            if (!$checkChange) {
                throw new \Exception("Not enough change available for item: " . $itemKey);
            }
            $this->transaction->setItem($item, $quantity);
            $item->decreaseQuantity($quantity);

            $this->cashBox->addCoins($this->transaction->getCoins());

            $changeAmount = $this->transaction->getBalance() - ($item->getPrice() * $quantity);
            $changeCoins = $this->cashBox->takeChange($changeAmount);
            $this->transaction->setCoins($changeCoins);
            $this->itemRepository->update($item); //Actualizar repositorio
            $this->cashBoxItemJsonRepository->updateAll($this->cashBox->getItems());
            return $item;
        } catch (\Throwable $th) {
            $this->rollbackTransaction();
            throw $th;
        }
    }

    public function getAvailableItems(): array
    {
        return $this->itemRepository->findAll();
    }

    public function restockItem(string $itemKey, int $quantity): void
    {
        $item = $this->itemRepository->findByKey($itemKey);
        if ($item) {
            $item->increaseQuantity($quantity);
            $this->itemRepository->update($item);
        } else {
            throw new \Exception("Item not found: " . $itemKey);
        }
    }

    public function getCashBoxStatus(): array
    {
        $coins = $this->cashBox->getItems();
        $balance = $this->cashBox->getTotalAmount();
        return ['coins' => $coins, 'balance' => $balance];
    }

    public function getAcceptedCoins(): array
    {
        return self::VALID_COIN_VALUES;
    }

    private function isCoinAccepted(Coin $coin): bool
    {
        //TODO: Debería validarse con una lista de monedas aceptadas
        return in_array($coin->getValue(), $this::VALID_COIN_VALUES);
    }
}

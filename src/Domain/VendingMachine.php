<?php

namespace App\Domain;

use App\Infrastructure\Repositories\ItemJsonRepository;
use App\Infrastructure\Repositories\CashBoxItemRepository;

class VendingMachine
{
    private ItemJsonRepository $itemRepository;
    private CashBoxItemRepository $cashBoxItemRepository;
    private CashBox $cashBox;
    private ?Transaction $transaction;


    public function __construct(ItemJsonRepository $itemRepository, CashBoxItemRepository $cashBoxItemRepository)
    {
        $this->itemRepository = $itemRepository;
        $this->cashBoxItemRepository = $cashBoxItemRepository;
        $this->cashBox = new CashBox($cashBoxItemRepository->findAll());
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
        if ($this->transaction === null) {
            throw new \Exception("No active transaction");
        }
        return $this->transaction;
    }

    public function closeTransaction(): void
    {
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

    public function buyItem(string $itemKey, int $quantity = 1): array
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
            $this->transaction->setReturnCoins($changeCoins);

            $this->itemRepository->update($item);
            $this->cashBoxItemRepository->updateAll($this->cashBox->getItems());
            return ['item' => $item, 'change' => array_merge($changeCoins, $this->transaction->getInvalidCoins())];
        } catch (\Throwable $th) {
            $this->rollbackTransaction();
            throw $th;
        }
    }

    public function refundTransaction(): array
    {
        return [$this->transaction->getCoins() , $this->transaction->getInvalidCoins()];
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
                $this->cashBoxItemRepository->updateAll($this->cashBox->getItems());
            }
        }
    }


    private function isCoinAccepted(Coin $coin): bool
    {
        $validValues = [0.05, 0.10, 0.25, 1.00];
        return in_array($coin->getValue(), $validValues);
    }
}

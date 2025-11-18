<?php

namespace App\Domain;

class CashBox
{
    /** var CashBoxItem[] */
    private array $cashBoxItems;

    public function __construct(array $cashBoxItems)
    {
        $this->cashBoxItems = $cashBoxItems;
    }

    public function addCoin(Coin $coin): void
    {
        $added = false;
        foreach ($this->cashBoxItems as $item) {
            if ($item->getCoin()->equals($coin)) {
                $item->increaseQuantity();
                $added = true;
                break;
            }
        }
        if (!$added) {
            $this->cashBoxItems[] = new CashBoxItem($coin, 1);
        }
    }

    public function takeCoin(Coin $coin): void
    {
        $taked = false;
        foreach ($this->cashBoxItems as $item) {
            if ($item->getCoin()->equals($coin)) {
                $item->decreaseQuantity();
                $taked = true;
                break;
            }
        }
        if (!$taked) {
            throw new \Exception("Coin not found in cash box");
        }
    }

    public function addCoins(array $coins): void
    {
        foreach ($coins as $coin) {
            $this->addCoin($coin);
        }
    }

    public function takeCoins(array $coins): void
    {
        foreach ($coins as $coin) {
            $this->takeCoin($coin);
        }
    }

    public function calculateChange(float $amount): array
    {
        if ($amount <= 0) {
            return [];
        }
        usort($this->cashBoxItems, function (CashBoxItem $a, CashBoxItem $b) {
            return $b->getCoin()->getValue() <=> $a->getCoin()->getValue();
        });
        $returnCoins = $this->calculateReturnCoins($amount * 100, $this->cashBoxItems);

        if ($amount < 0 && $returnCoins === null) {
            throw new \Exception("Not enough change available");
        }
        return $returnCoins;
    }

    public function takeChange(float $amount): array
    {
        if ($amount <= 0) {
            return [];
        }
        $changeCoins = $this->calculateChange($amount);
        foreach ($changeCoins as $coin) {
            $this->takeCoin($coin);
        }
        return $changeCoins;
    }

    public function checkChangeAvailable(float $amount, array $addedCoins): bool
    {
        $cashBoxItemsShadow = unserialize(serialize($this->cashBoxItems));
        $availableCashBoxItems = $this->addCoinsHelper($addedCoins, $cashBoxItemsShadow);
        usort($availableCashBoxItems, function (CashBoxItem $a, CashBoxItem $b) {
            return $b->getCoin()->getValue() <=> $a->getCoin()->getValue();
        });
        $returnCoins = $this->calculateReturnCoins($amount * 100, $availableCashBoxItems);
        return $returnCoins !== null;
    }

    public function getTotalAmount(): float
    {
        $total = 0;
        foreach ($this->cashBoxItems as $item) {
            $total += $item->getTotalAmount();
        }
        return $total;
    }

    public function getItems(): array
    {
        return $this->cashBoxItems;
    }

    private function calculateReturnCoins(
        int $amount,
        array $cashBoxItems,
        array $returnCoins = [],
        int $depth = 0
    ): ?array {
        if ($amount == 0) {
            return $returnCoins;
        }
        if ($amount < 0 || $depth > 100) {
            return null; //TODO: ideal una excepción personalizada
        }

        foreach ($cashBoxItems as $cashBoxItem) {
            if ($cashBoxItem->getQuantity() > 0) {
                $cashBoxItem->decreaseQuantity();
                $currentReturnCoins = unserialize(serialize($returnCoins));
                $currentReturnCoins[] = $cashBoxItem->getCoin();
                $result = $this->calculateReturnCoins(
                    $amount - $cashBoxItem->getCoin()->toIntegerAmount(),
                    $cashBoxItems,
                    $currentReturnCoins,
                    $depth + 1
                );
                if ($result !== null) {
                    return $result;
                }

                if (!$this->hasCoins($cashBoxItems)) {
                    break;
                }

                $cashBoxItem->increaseQuantity();
            }
        }
        return null;
    }

    private function hasCoins(array $cashBoxItems): bool
    {
        foreach ($cashBoxItems as $cashBoxItem) {
            if ($cashBoxItem->getQuantity() > 0) {
                return true;
            }
        }
        return false;
    }

    private function addCoinsHelper(array $addedCoins, array $cashBoxItems): array
    {
        foreach ($addedCoins as $coin) {
            $cashBoxFounded = null;
            foreach ($cashBoxItems as $item) {
                if ($item->getCoin()->equals($coin)) {
                    $cashBoxFounded = $item;
                    break;
                }
            }
            if ($cashBoxFounded) {
                $cashBoxFounded->increaseQuantity();
            } else {
                $cashBoxItems[] = new CashBoxItem($coin, 1);
            }
        }
        return $cashBoxItems;
    }
}

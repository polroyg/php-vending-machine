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
        $cashBoxItemsShadow = array_map(fn($item) => clone $item, $this->cashBoxItems);
        $returnCoins = $this->calculateReturnCoins((int)($amount * 100), $cashBoxItemsShadow);

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
        $cashBoxItemsShadow = array_map(fn($item) => clone $item, $this->cashBoxItems);
        $availableCashBoxItems = $this->addCoinsHelper($addedCoins, $cashBoxItemsShadow);

        usort($availableCashBoxItems, function (CashBoxItem $a, CashBoxItem $b) {
            return $b->getCoin()->getValue() <=> $a->getCoin()->getValue();
        });


        $returnCoins = $this->calculateReturnCoins((int)($amount * 100), $availableCashBoxItems);

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

        if ($depth == 0) {
            $sumAvailable = array_reduce(
                $cashBoxItems,
                fn($sum, CashBoxItem $item) => $sum + $item->getTotalAmount(),
                0
            );
            if ((int)($sumAvailable * 100) < $amount) {
                return null;
            } elseif ((int)($sumAvailable * 100) === $amount) {
                //get all moneys
                return array_merge(...array_map(
                    function ($item) {
                        return array_fill(0, $item->getQuantity(), $item->getCoin());
                    },
                    $cashBoxItems
                ));
            }
        }

        if ($amount == 0) {
            return $returnCoins;
        }

        if ($amount < 0 || $depth > 10) {
            return null; //TODO: ideal una excepción personalizada
        }

        foreach ($cashBoxItems as $cashBoxItem) {
            if ($cashBoxItem->getQuantity() > 0) {
                // Simulate taking a coin by decreasing quantity
                $cashBoxItem->decreaseQuantity();
                $currentReturnCoins = $returnCoins;
                $currentReturnCoins[] = $cashBoxItem->getCoin();
                $result = $this->calculateReturnCoins(
                    $amount - $cashBoxItem->getCoin()->toIntegerAmount(),
                    $cashBoxItems,
                    $currentReturnCoins,
                    $depth + 1
                );
                // Restore quantity after simulation
                $cashBoxItem->increaseQuantity();
                if ($result !== null) {
                    return $result;
                }

                if (!$this->hasCoins($cashBoxItems)) {
                    break;
                }
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

    //clone method
    public function __clone()
    {
        $clonedCashBoxItems = [];
        foreach ($this->cashBoxItems as $item) {
            $clonedCashBoxItems[] = clone $item;
        }
        $this->cashBoxItems = $clonedCashBoxItems;
    }
}

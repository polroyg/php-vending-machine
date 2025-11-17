<?php

namespace App\Domain;

class Transaction
{
    /** var Coin[] */
    private array $coins = [];
    /** var float[] */
    private array $noAcceptedCoins = [];
    private ?Item $item;
    private float $balance = 0;
    public function __construct(?array $coins = null, ?Item $item = null)
    {
        $this->coins = $coins;
        $this->item = $item;
        if (null != $coins) {
            $this->balance = array_reduce($coins, fn($sum, Coin $coin) => $sum + $coin->getValue(), 0);
        }
    }

    public function addCoin(Coin $coin): void
    {
        $this->coins[] = $coin;
        $this->balance += $coin->getValue();
    }

    public function addInvalidCoinValue(float $value): void
    {
        $this->noAcceptedCoins[] = $value;
    }

    public function setItem($item): void
    {
        $this->item = $item;
    }

    public function getBalance(): float
    {
        return $this->balance;
    }

    public function refund(): array
    {
        return $this->coins;
    }

    public function isValid(): bool
    {
        if (null === $this->item) {
            return false;
        } elseif ($this->item->getPrice() > $this->balance) {
            return false;
        } elseif ($this->item->getQuantity() < 1) {
            return false;
        }
        return true;
    }
}

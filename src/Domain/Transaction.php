<?php

namespace App\Domain;

class Transaction
{
    /** var Coin[] */
    private array $coins = [];
    /** var Coin[] */
    private array $invalidCoins = [];

    private array $returnCoins = [];

    private ?Item $item;
    private int $quantity = 1;
    private float $balance = 0;



    public function __construct(?array $coins = null, ?Item $item = null)
    {
        $this->coins = $coins ?? [];
        $this->item = $item;
        $this->balance = array_reduce($this->coins, fn($sum, Coin $coin) => $sum + $coin->getValue(), 0);
    }

    public function addCoin(Coin $coin): void
    {
        $this->coins[] = $coin;
        $this->balance += $coin->getValue();
    }

    public function addInvalidCoin(Coin $coin): void
    {
        $this->invalidCoins[] = $coin;
    }

    public function getItem(): ?Item
    {
        return $this->item;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setItem($item, int $quantity = 1): void
    {
        $this->item = $item;
        $this->quantity = $quantity;
    }

    public function getBalance(): float
    {
        return $this->balance;
    }

    public function refund(): array
    {
        return $this->coins;
    }

    public function setReturnCoins(array $coins): void
    {
        $this->returnCoins = $coins;
    }

    public function getCoins(): array
    {
        return $this->coins;
    }

    public function getInvalidCoins(): array
    {
        return $this->invalidCoins;
    }

    public function clearCoins(): void
    {
        $this->coins = [];
        $this->invalidCoins = [];
        $this->balance = 0;
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

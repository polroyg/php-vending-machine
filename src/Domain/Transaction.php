<?php

namespace App\Domain;

class Transaction
{
    private array $coins = [];
    private ?Item $item;
    private float $balance = 0;
    public function __construct(array $coins, ?Item $item)
    {
        $this->coins = $coins;
        $this->item = $item;
        $this->balance = array_reduce($coins, fn ($sum, Coin $coin) => $sum + $coin->getValue(), 0);
    }

    public function addCoin(Coin $coin): void
    {
        $this->coins[] = $coin;
        $this->balance += $coin->getValue();
    }

    public function getBalance(): float
    {
        return $this->balance;
    }

    public function refund(): array
    {
        return $this->coins;
    }
}

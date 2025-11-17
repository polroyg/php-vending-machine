<?php

namespace App\Domain;

class CashBoxItem
{
    private Coin $coin;
    private int $quantity;

    public function __construct(Coin $coin, int $quantity)
    {
        if ($quantity < 0) {
            throw new \InvalidArgumentException("Invalid quantity value: $quantity");
        }
        $this->coin = $coin;
        $this->quantity = $quantity;
    }

    public function getCoin(): Coin
    {
        return $this->coin;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function totalImport(): float
    {
        return $this->coin->getValue() * $this->quantity;
    }
}

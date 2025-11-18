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

    public function increaseQuantity(int $amount = 1): void
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException("Invalid amount value: $amount");
        }
        $this->quantity += $amount;
    }

    public function decreaseQuantity(int $amount = 1): void
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException("Invalid amount value: $amount");
        }
        if ($this->quantity - $amount < 0) {
            throw new \InvalidArgumentException("Quantity cannot be negative");
        }
        $this->quantity -= $amount;
    }

    public function getTotalAmount(): float
    {
        return $this->coin->getValue() * $this->quantity;
    }

    public function equals(CashBoxItem $other): bool
    {
        return $this->coin->equals($other->getCoin());
    }
}

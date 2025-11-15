<?php

namespace App\Domain;

class Item
{
    private string $name;
    private float $price;
    private int $quantity;

    public function __construct(string $name, float $price, int $quantity)
    {
        if ($price <= 0) {
            throw new \InvalidArgumentException("Price must be positive: $price");
        }
        if ($quantity < 0) {
            throw new \InvalidArgumentException("Quantity cannot be negative: $quantity");
        }
        $this->name = $name;
        $this->price = $price;
        $this->quantity = $quantity;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        if ($quantity < 0) {
            throw new \InvalidArgumentException("Quantity cannot be negative: $quantity");
        }
        $this->quantity = $quantity;
    }

    public function addQuantity(int $amount): void
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException("Amount to add cannot be negative: $amount");
        }
        $this->quantity += $amount;
    }

    public function removeQuantity(int $amount): void
    {
        if ($amount < 0 || $amount > $this->quantity) {
            throw new \InvalidArgumentException("Amount to remove is not valid: $amount / {$this->quantity}");
        }
        $this->quantity -= $amount;
    }
}

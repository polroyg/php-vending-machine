<?php

namespace Domain;

class Coin
{
    private float $value;
    private int $quantity;

    public function __construct(float $value, int $quantity)
    {
        if (!$this->validateValue($value)) {
            throw new \InvalidArgumentException("Invalid coin value: $value");
        }
        if ($quantity < 0) {
            throw new \InvalidArgumentException("Quantity cannot be negative: $quantity");
        }
        $this->value = $value;
        $this->quantity = $quantity;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
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

    private function validateValue(float $value): bool
    {
        $validValues = [0.05, 0.10, 0.25, 1.00];
        return in_array($value, $validValues, true);
    }
}

<?php

namespace App\Domain;

class Coin
{
    private float $value;

    public function __construct(float $value)
    {
        if (!$this->validateValue($value)) {
            throw new \InvalidArgumentException("Invalid coin value: $value");
        }
        $this->value = $value;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    private function validateValue(float $value): bool
    {
        $validValues = [0.05, 0.10, 0.25, 1.00];
        return in_array($value, $validValues, true);
    }

    public function equals(Coin $other): bool
    {
        return $this->value === $other->getValue();
    }
}

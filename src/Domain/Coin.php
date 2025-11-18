<?php

namespace App\Domain;

class Coin
{
    private float $value;
    private const VALID_VALUES = [0.02, 0.05, 0.10, 0.25, 0.50, 1.00, 2.00];

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

    public function toIntegerAmount(): int
    {
        return (int)round($this->value * 100);
    }

    private function validateValue(float $value): bool
    {
        return in_array($value, self::VALID_VALUES, true);
    }

    public function equals(Coin $other): bool
    {
        return $this->value === $other->getValue();
    }
}

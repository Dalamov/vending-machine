<?php

declare(strict_types=1);

namespace Daniella\VendingMachine;

class ChangeTray
{
    private float $insertedAmount = 0.0;
    
    /** @var array<float, int> */
    private array $availableChange = [
        0.05 => 0,
        0.10 => 0,
        0.25 => 0,
        1.00 => 0,
    ];

    public function __construct(array $availableChange = [])
    {
        if (!empty($availableChange)) {
            $this->availableChange = $availableChange;
        }
    }

    public function insertCoin(float $amount): void
    {
        if (!$this->isValidCoin($amount)) {
            throw new \InvalidArgumentException("Invalid coin amount: {$amount}");
        }

        $this->insertedAmount += $amount;
    }

    public function getInsertedAmount(): float
    {
        return $this->insertedAmount;
    }

    public function returnCoins(): array
    {
        $returned = $this->calculateCoinsToReturn($this->insertedAmount);
        $this->insertedAmount = 0.0;
        return $returned;
    }

    public function calculateChange(float $price): array
    {
        $changeAmount = $this->insertedAmount - $price;
        
        if ($changeAmount < 0) {
            throw new \RuntimeException("Insufficient funds. Need " . ($price - $this->insertedAmount) . " more.");
        }

        if ($changeAmount === 0.0) {
            $this->insertedAmount = 0.0;
            return [];
        }

        $change = $this->calculateCoinsToReturn($changeAmount);
        $this->deductChangeFromAvailable($change);
        $this->insertedAmount = 0.0;

        return $change;
    }

    public function hasEnoughChange(float $changeAmount): bool
    {
        if ($changeAmount === 0.0) {
            return true;
        }

        $neededCoins = $this->calculateCoinsToReturn($changeAmount);
        
        foreach ($neededCoins as $coin => $count) {
            if ($this->availableChange[$coin] < $count) {
                return false;
            }
        }

        return true;
    }

    public function setAvailableChange(array $change): void
    {
        $this->availableChange = $change;
    }

    public function getAvailableChange(): array
    {
        return $this->availableChange;
    }

    private function isValidCoin(float $amount): bool
    {
        return in_array($amount, [0.05, 0.10, 0.25, 1.00], true);
    }

    private function calculateCoinsToReturn(float $amount): array
    {
        $coins = [1.00, 0.25, 0.10, 0.05];
        $result = [];
        $remaining = round($amount, 2);

        foreach ($coins as $coin) {
            if ($remaining >= $coin) {
                $count = (int) floor($remaining / $coin);
                $result[$coin] = $count;
                $remaining = round($remaining - ($count * $coin), 2);
            }
        }

        return $result;
    }

    private function deductChangeFromAvailable(array $change): void
    {
        foreach ($change as $coin => $count) {
            $this->availableChange[$coin] -= $count;
        }
    }
}


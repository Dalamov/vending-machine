<?php

declare(strict_types=1);

namespace Daniella\VendingMachine;

class VendingMachine
{
    private Inventory $inventory;
    private ChangeTray $changeTray;

    public function __construct(?Inventory $inventory = null, ?ChangeTray $changeTray = null)
    {
        $this->inventory = $inventory ?? new Inventory();
        $this->changeTray = $changeTray ?? new ChangeTray();
    }

    public function insertMoney(float $amount): void
    {
        $this->changeTray->insertCoin($amount);
    }

    public function returnCoin(): array
    {
        return $this->changeTray->returnCoins();
    }

    public function selectItem(string $itemName): array
    {
        $normalizedName = $this->normalizeItemName($itemName);
        $item = $this->inventory->findItemByName($normalizedName);

        if ($item === null) {
            throw new \RuntimeException("Item not found: {$itemName}");
        }

        if ($item->getQuantity() <= 0) {
            throw new \RuntimeException("Item out of stock: {$normalizedName}");
        }

        $price = $item->getPrice();
        $insertedAmount = $this->changeTray->getInsertedAmount();

        if ($insertedAmount < $price) {
            throw new \RuntimeException("Insufficient funds. Need " . ($price - $insertedAmount) . " more.");
        }

        $changeAmount = $insertedAmount - $price;

        if ($changeAmount > 0 && !$this->changeTray->hasEnoughChange($changeAmount)) {
            throw new \RuntimeException("Not enough change available.");
        }

        $item->decreaseQuantity();
        $change = $this->changeTray->calculateChange($price);

        $result = [$normalizedName];
        
        foreach ($change as $coin => $count) {
            for ($i = 0; $i < $count; $i++) {
                $result[] = $coin;
            }
        }
        
        return $result;
    }

    public function service(array $change, array $items): void
    {
        $this->changeTray->setAvailableChange($change);
        
        foreach ($items as $item) {
            if (!$item instanceof Item) {
                throw new \InvalidArgumentException("All items must be Item instances");
            }
        }
        
        $this->inventory = new Inventory($items);
    }

    public function getInventory(): Inventory
    {
        return $this->inventory;
    }

    public function getChangeTray(): ChangeTray
    {
        return $this->changeTray;
    }

    private function normalizeItemName(string $name): string
    {
        $name = strtoupper(trim($name));
        
        $mapping = [
            'GET-WATER' => 'Water',
            'GET-JUICE' => 'Juice',
            'GET-SODA' => 'Soda',
            'WATER' => 'Water',
            'JUICE' => 'Juice',
            'SODA' => 'Soda',
        ];

        return $mapping[$name] ?? $name;
    }
}
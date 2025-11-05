<?php

declare(strict_types=1);

namespace Daniella\VendingMachine;

class Inventory
{
    private array $items;

    public function __construct(array $items = [])
    {
        if (empty($items)) {
            $this->items = [
                new Item('Water', 0.65, 10),
                new Item('Juice', 1.00, 10),
                new Item('Soda', 1.50, 10),
            ];
        } else {
            $this->items = $items;
        }
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function addItem(Item $item): void
    {
        $this->items[] = $item;
    }

    public function removeItem(Item $item): void
    {
        foreach ($this->items as $key => $inventoryItem) {
            if ($inventoryItem === $item) {
                unset($this->items[$key]);
                break;
            }
        }
    }

    public function findItemByName(string $name): ?Item
    {
        foreach ($this->items as $item) {
            if ($item->getName() === $name) {
                return $item;
            }
        }
        return null;
    }
}
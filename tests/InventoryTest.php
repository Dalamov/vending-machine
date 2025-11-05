<?php

declare(strict_types=1);

namespace Tests\Daniella\VendingMachine;

use Daniella\VendingMachine\Inventory;
use Daniella\VendingMachine\Item;
use PHPUnit\Framework\TestCase;

class InventoryTest extends TestCase
{
    public function testInventoryInitializesWithDefaultItems(): void
    {
        $inventory = new Inventory();

        $items = $inventory->getItems();

        $this->assertCount(3, $items);
        $this->assertEquals('Water', $items[0]->getName());
        $this->assertEquals(0.65, $items[0]->getPrice());
        $this->assertEquals('Juice', $items[1]->getName());
        $this->assertEquals(1.00, $items[1]->getPrice());
        $this->assertEquals('Soda', $items[2]->getName());
        $this->assertEquals(1.50, $items[2]->getPrice());
    }

    public function testInventoryCanBeCreatedWithCustomItems(): void
    {
        $customItems = [
            new Item('Custom1', 0.50, 5),
            new Item('Custom2', 2.00, 3),
        ];

        $inventory = new Inventory($customItems);

        $this->assertCount(2, $inventory->getItems());
        $this->assertEquals('Custom1', $inventory->getItems()[0]->getName());
    }

    public function testAddItemAddsItemToInventory(): void
    {
        $inventory = new Inventory();
        $newItem = new Item('NewItem', 0.75, 5);

        $inventory->addItem($newItem);

        $this->assertCount(4, $inventory->getItems());
        $this->assertEquals('NewItem', $inventory->getItems()[3]->getName());
    }

    public function testRemoveItemRemovesItemFromInventory(): void
    {
        $inventory = new Inventory();
        $items = $inventory->getItems();
        $itemToRemove = $items[0];

        $inventory->removeItem($itemToRemove);

        $this->assertCount(2, $inventory->getItems());
        $this->assertNotContains($itemToRemove, $inventory->getItems());
    }

    public function testFindItemByNameReturnsItemWhenFound(): void
    {
        $inventory = new Inventory();

        $item = $inventory->findItemByName('Water');

        $this->assertInstanceOf(Item::class, $item);
        $this->assertEquals('Water', $item->getName());
    }

    public function testFindItemByNameReturnsNullWhenNotFound(): void
    {
        $inventory = new Inventory();

        $item = $inventory->findItemByName('NonExistent');

        $this->assertNull($item);
    }
}


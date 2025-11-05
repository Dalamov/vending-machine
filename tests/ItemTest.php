<?php

declare(strict_types=1);

namespace Tests\Daniella\VendingMachine;

use Daniella\VendingMachine\Item;
use PHPUnit\Framework\TestCase;

class ItemTest extends TestCase
{
    public function testItemCanBeCreated(): void
    {
        $item = new Item('Water', 0.65, 10);

        $this->assertEquals('Water', $item->getName());
        $this->assertEquals(0.65, $item->getPrice());
        $this->assertEquals(10, $item->getQuantity());
    }

    public function testDecreaseQuantityDecrementsQuantity(): void
    {
        $item = new Item('Water', 0.65, 10);

        $item->decreaseQuantity();

        $this->assertEquals(9, $item->getQuantity());
    }

    public function testDecreaseQuantityThrowsExceptionWhenOutOfStock(): void
    {
        $item = new Item('Water', 0.65, 0);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Item out of stock.');

        $item->decreaseQuantity();
    }

    public function testDecreaseQuantityThrowsExceptionWhenQuantityIsNegative(): void
    {
        $item = new Item('Water', 0.65, 1);
        $item->decreaseQuantity();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Item out of stock.');

        $item->decreaseQuantity();
    }
}


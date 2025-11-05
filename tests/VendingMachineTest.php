<?php

declare(strict_types=1);

namespace Tests\Daniella\VendingMachine;

use Daniella\VendingMachine\ChangeTray;
use Daniella\VendingMachine\Inventory;
use Daniella\VendingMachine\Item;
use Daniella\VendingMachine\VendingMachine;
use PHPUnit\Framework\TestCase;

class VendingMachineTest extends TestCase
{
    public function testInsertMoneyAddsMoney(): void
    {
        $machine = new VendingMachine();

        $machine->insertMoney(0.25);
        $machine->insertMoney(0.10);

        $this->assertEquals(0.35, $machine->getChangeTray()->getInsertedAmount());
    }

    public function testReturnCoinReturnsAllInsertedCoins(): void
    {
        $machine = new VendingMachine();
        $machine->insertMoney(0.10);
        $machine->insertMoney(0.10);

        $returned = $machine->returnCoin();

        $this->assertEquals([0.10 => 2], $returned);
        $this->assertEquals(0.0, $machine->getChangeTray()->getInsertedAmount());
    }

    public function testSelectItemReturnsItemWhenExactChange(): void
    {
        $changeTray = new ChangeTray([
            0.05 => 10,
            0.10 => 10,
            0.25 => 10,
            1.00 => 10,
        ]);
        $machine = new VendingMachine(null, $changeTray);
        $machine->insertMoney(1.00);
        $machine->insertMoney(0.25);
        $machine->insertMoney(0.25);

        $result = $machine->selectItem('GET-SODA');

        $this->assertEquals(['Soda'], $result);
    }

    public function testSelectItemReturnsItemWithChangeWhenOverpaid(): void
    {
        $changeTray = new ChangeTray([
            0.05 => 10,
            0.10 => 10,
            0.25 => 10,
            1.00 => 10,
        ]);
        $machine = new VendingMachine(null, $changeTray);
        $machine->insertMoney(1.00);

        $result = $machine->selectItem('GET-WATER');

        $this->assertContains('Water', $result);
        // Verificar que hay cambio devuelto (más de solo "Water")
        // El cambio de 0.35 debería resultar en Water + monedas de cambio
        $this->assertGreaterThan(1, count($result), 'Should return Water plus change coins');
    }

    public function testSelectItemThrowsExceptionWhenItemNotFound(): void
    {
        $machine = new VendingMachine();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Item not found');

        $machine->selectItem('GET-NONEXISTENT');
    }

    public function testSelectItemThrowsExceptionWhenOutOfStock(): void
    {
        $item = new Item('Water', 0.65, 0);
        $inventory = new Inventory([$item]);
        $machine = new VendingMachine($inventory);
        $machine->insertMoney(1.00);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Item out of stock');

        $machine->selectItem('GET-WATER');
    }

    public function testSelectItemThrowsExceptionWhenInsufficientFunds(): void
    {
        $machine = new VendingMachine();
        $machine->insertMoney(0.25);
        $machine->insertMoney(0.25);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Insufficient funds');

        $machine->selectItem('GET-WATER');
    }

    public function testSelectItemThrowsExceptionWhenNotEnoughChange(): void
    {
        $changeTray = new ChangeTray([
            0.05 => 0,
            0.10 => 0,
            0.25 => 0,
            1.00 => 0,
        ]);
        $machine = new VendingMachine(null, $changeTray);
        $machine->insertMoney(1.00);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Not enough change available');

        $machine->selectItem('GET-WATER');
    }

    public function testServiceSetsAvailableChangeAndItems(): void
    {
        $machine = new VendingMachine();
        $newChange = [
            0.05 => 20,
            0.10 => 20,
            0.25 => 20,
            1.00 => 20,
        ];
        $newItems = [
            new Item('NewItem1', 0.50, 5),
            new Item('NewItem2', 2.00, 3),
        ];

        $machine->service($newChange, $newItems);

        $this->assertEquals($newChange, $machine->getChangeTray()->getAvailableChange());
        $this->assertCount(2, $machine->getInventory()->getItems());
    }

    public function testServiceThrowsExceptionWhenItemsAreNotItemInstances(): void
    {
        $machine = new VendingMachine();
        $invalidItems = ['not an item', 'also not an item'];

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('All items must be Item instances');

        $machine->service([], $invalidItems);
    }

    // Example 1 from spec: Buy Soda with exact change
    public function testExample1BuySodaWithExactChange(): void
    {
        $changeTray = new ChangeTray([
            0.05 => 10,
            0.10 => 10,
            0.25 => 10,
            1.00 => 10,
        ]);
        $machine = new VendingMachine(null, $changeTray);
        $machine->insertMoney(1.00);
        $machine->insertMoney(0.25);
        $machine->insertMoney(0.25);

        $result = $machine->selectItem('GET-SODA');

        $this->assertEquals(['Soda'], $result);
    }

    // Example 2 from spec: Return coin
    public function testExample2ReturnCoin(): void
    {
        $machine = new VendingMachine();
        $machine->insertMoney(0.10);
        $machine->insertMoney(0.10);

        $result = $machine->returnCoin();

        $this->assertEquals([0.10 => 2], $result);
    }

    // Example 3 from spec: Buy Water without exact change
    public function testExample3BuyWaterWithoutExactChange(): void
    {
        $changeTray = new ChangeTray([
            0.05 => 10,
            0.10 => 10,
            0.25 => 10,
            1.00 => 10,
        ]);
        $machine = new VendingMachine(null, $changeTray);
        $machine->insertMoney(1.00);

        $result = $machine->selectItem('GET-WATER');

        $this->assertContains('Water', $result);
        // Verificar que hay cambio devuelto (más de solo "Water")
        // El cambio de 0.35 debería resultar en Water + monedas de cambio
        $this->assertGreaterThan(1, count($result), 'Should return Water plus change coins');
    }
}


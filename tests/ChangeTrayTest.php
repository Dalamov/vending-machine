<?php

declare(strict_types=1);

namespace Tests\Daniella\VendingMachine;

use Daniella\VendingMachine\ChangeTray;
use PHPUnit\Framework\TestCase;

class ChangeTrayTest extends TestCase
{
    public function testInsertCoinAddsToInsertedAmount(): void
    {
        $changeTray = new ChangeTray();

        $changeTray->insertCoin(0.25);
        $changeTray->insertCoin(0.10);

        $this->assertEquals(0.35, $changeTray->getInsertedAmount());
    }

    public function testInsertCoinThrowsExceptionForInvalidCoin(): void
    {
        $changeTray = new ChangeTray();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid coin amount');

        $changeTray->insertCoin(0.50);
    }

    public function testReturnCoinsReturnsAllInsertedCoins(): void
    {
        $changeTray = new ChangeTray();
        $changeTray->insertCoin(0.10);
        $changeTray->insertCoin(0.10);

        $returned = $changeTray->returnCoins();

        $this->assertEquals([0.10 => 2], $returned); // Array asociativo interno
        $this->assertEquals(0.0, $changeTray->getInsertedAmount());
    }

    public function testCalculateChangeReturnsCorrectCoins(): void
    {
        $changeTray = new ChangeTray([
            0.05 => 10,
            0.10 => 10,
            0.25 => 10,
            1.00 => 10,
        ]);
        $changeTray->insertCoin(1.00);

        $change = $changeTray->calculateChange(0.65);

        $this->assertEquals([0.25 => 1, 0.10 => 1], $change);
        $this->assertEquals(0.0, $changeTray->getInsertedAmount());
    }

    public function testCalculateChangeReturnsEmptyArrayWhenNoChange(): void
    {
        $changeTray = new ChangeTray();
        $changeTray->insertCoin(1.00);

        $change = $changeTray->calculateChange(1.00);

        $this->assertEquals([], $change);
    }

    public function testCalculateChangeThrowsExceptionWhenInsufficientFunds(): void
    {
        $changeTray = new ChangeTray([
            0.05 => 10,
            0.10 => 10,
            0.25 => 10,
            1.00 => 10,
        ]);
        $changeTray->insertCoin(0.25);
        $changeTray->insertCoin(0.25);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Insufficient funds');

        $changeTray->calculateChange(1.00);
    }

    public function testHasEnoughChangeReturnsTrueWhenChangeIsAvailable(): void
    {
        $changeTray = new ChangeTray([
            0.05 => 10,
            0.10 => 10,
            0.25 => 10,
            1.00 => 10,
        ]);

        $this->assertTrue($changeTray->hasEnoughChange(0.35));
    }

    public function testHasEnoughChangeReturnsFalseWhenChangeIsNotAvailable(): void
    {
        $changeTray = new ChangeTray([
            0.05 => 0,
            0.10 => 0,
            0.25 => 0, // No tiene monedas de 0.25
            1.00 => 0,
        ]);

        // Necesita monedas para 0.35 pero no tiene ninguna disponible
        $this->assertFalse($changeTray->hasEnoughChange(0.35));
    }

    public function testSetAvailableChangeUpdatesAvailableChange(): void
    {
        $changeTray = new ChangeTray();
        $newChange = [
            0.05 => 5,
            0.10 => 5,
            0.25 => 5,
            1.00 => 5,
        ];

        $changeTray->setAvailableChange($newChange);

        $this->assertEquals($newChange, $changeTray->getAvailableChange());
    }
}


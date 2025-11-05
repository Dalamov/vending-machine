# Vending Machine

A PHP implementation of a vending machine simulator for a coding challenge.

## Description

This project models a vending machine that accepts coins, dispenses items, and handles change. The machine supports three primary items (Water, Juice, and Soda) with specific prices and handles coin insertion, item selection, and change calculation.

## Requirements

- PHP 8.1 or higher
- Composer
- PHPUnit 12.4 (for testing)

## Installation

### Using Composer (Local)

1. Clone the repository:
```bash
git clone <repository-url>
cd vending-machine
```

2. Install dependencies:
```bash
composer install
```

### Using Docker

1. Build the Docker image:
```bash
docker build -t vending-machine .
```

2. Or use docker-compose:
```bash
docker-compose build
```

## How to Run

### Run Tests (Local)

```bash
vendor/bin/phpunit
```

Or with testdox output:
```bash
vendor/bin/phpunit --testdox
```

### Run Tests (Docker)

Using Docker directly:
```bash
docker run --rm -v $(pwd):/app vending-machine
```

Using docker-compose:
```bash
docker-compose run --rm vending-machine
```

### Interactive Shell (Docker)

To get an interactive shell inside the container:

```bash
docker-compose run --rm vending-machine /bin/bash
```

Then you can run PHPUnit or PHP commands:
```bash
vendor/bin/phpunit
php -r "echo 'Hello from PHP!';"
```

## Project Structure

```
vending-machine/
├── src/
│   ├── VendingMachine.php    # Main vending machine class
│   ├── Inventory.php          # Manages available items
│   ├── Item.php              # Represents a single item
│   └── ChangeTray.php        # Handles money and change
├── tests/
│   ├── VendingMachineTest.php
│   ├── InventoryTest.php
│   ├── ItemTest.php
│   └── ChangeTrayTest.php
├── composer.json
├── phpunit.xml
├── Dockerfile
└── docker-compose.yml
```

## Features

### Valid Actions

- **Insert Money**: Accepts coins of 0.05, 0.10, 0.25, and 1.00
- **Return Coin**: Returns all inserted money
- **Select Item**: GET-WATER, GET-JUICE, GET-SODA
- **Service Mode**: Configure available change and items

### Items

- **Water**: $0.65
- **Juice**: $1.00
- **Soda**: $1.50

### State Tracking

- Available items (count, price, selector)
- Available change (number of coins available)
- Currently inserted money

## Examples

### Example 1: Buy Soda with exact change
```
Input: 1, 0.25, 0.25, GET-SODA
Output: SODA
```

### Example 2: Return coin
```
Input: 0.10, 0.10, RETURN-COIN
Output: 0.10, 0.10
```

### Example 3: Buy Water without exact change
```
Input: 1, GET-WATER
Output: WATER, 0.25, 0.10
```

## Running Tests

All tests can be run with:

```bash
vendor/bin/phpunit
```

To run specific test files:

```bash
vendor/bin/phpunit tests/VendingMachineTest.php
```

## License

MIT

## Author

Daniella Alamo - dalamov08@gmail.com


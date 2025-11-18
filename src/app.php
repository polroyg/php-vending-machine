#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Domain\VendingMachine;
use App\Domain\Coin;
use App\Infrastructure\ConsoleIO;
use App\Infrastructure\Repositories\CashBoxItemJsonRepository;
use App\Infrastructure\Repositories\ItemJsonRepository;
use App\Infrastructure\JsonStorage;

set_time_limit(0);
try {
    $consoleIO = new ConsoleIO();
    showStartMessage($consoleIO);

    $itemRepository = new ItemJsonRepository(new JsonStorage('./data/items.json'));
    $cashBoxItemRepository = new CashBoxItemJsonRepository(new JsonStorage('./data/cashbox.json'));

    loadData($itemRepository, $cashBoxItemRepository);

    $vendingMachine = new VendingMachine($itemRepository, $cashBoxItemRepository);

    do {
        $action = getMainAction($consoleIO);
        if ($action === 2) {
            processServiceActions($consoleIO);
        } elseif ($action === 1) {
            processCustomerActions($consoleIO, $vendingMachine);
        }
    } while ($action != 0);
    $consoleIO->showMessage("Thank you for using the vending machine. Goodbye!" . PHP_EOL);
} catch (\Throwable $th) {
    //throw $th;
    print_r("An error has occurred: " . $th->getMessage() . PHP_EOL);
}

function showStartMessage($consoleIO): void
{
    $consoleIO->showMessage("████████████████████████");
    $consoleIO->showMessage("█   VENDING MACHINE    █");
    $consoleIO->showMessage("████████████████████████");
    $consoleIO->showMessage("");
}

function getMainAction($consoleIO): int
{
    $consoleIO->showMainMenu();
    $consoleIO->showMessageInline("Enter your choice: ");
    $action = (int)fgets(STDIN);
    $consoleIO->showMessage("");
    return $action;
}

function processServiceActions($consoleIO)
{
    $consoleIO->showServiceMenu();
}


function processCustomerActions($consoleIO, $vendingMachine)
{
    $vendingMachine->startTransaction();
    do {
        $consoleIO->showMessage("*****************\n");
        $consoleIO->getCustomerMenu();
        $consoleIO->showMessageInline("Enter your choice: ");

        $action = (int)fgets(STDIN);

        if ($action === 1) {
            $consoleIO->showMessage("** Accepted coins [" . implode(", ", $vendingMachine->getAcceptedCoins()) . "] **");
            $consoleIO->showMessageInline("Insert coin value: ");
            $coinValue = (float)fgets(STDIN);
            try {
                $coin = new Coin($coinValue);
                $vendingMachine->addCoinToTransaction($coin);
                $consoleIO->showMessage("Inserted coin: " . number_format($coinValue, 2));
            } catch (\InvalidArgumentException $e) {
                $consoleIO->showMessage("Error: " . $e->getMessage());
            } catch (\Exception $e) {
                $consoleIO->showMessage("Error: " . $e->getMessage());
            }
        } elseif ($action === 2) {
            $products_list = $vendingMachine->getAvailableItems();
            $products_str = array_map(
                fn($item) => $item->getKey() . " => " . $item->getName() . "(" . number_format($item->getPrice(), 2) . ") - " . $item->getQuantity(),
                $products_list
            );
            $consoleIO->showMessage("** Available items [" . implode(", ", $products_str) . "] **");
            $consoleIO->showMessageInline("Insert item key: ");
            $itemKey = trim(fgets(STDIN));
            try {
                $result = $vendingMachine->buyItem($itemKey);
                if (isset($result['item'])) {
                    $consoleIO->showMessage("Dispensed item: " . $itemKey);
                }
                if (isset($result['change']) && count($result['change']) > 0) {
                    $coins_str = array_map(fn($coin) => number_format($coin->getValue(), 2), $result['change']);
                    $consoleIO->showMessage("Returned change: " . implode(", ", $coins_str));
                }
            } catch (\Exception $e) {
                $consoleIO->showMessage("Error: " . $e->getMessage());
                $return_coins = $vendingMachine->refundTransaction();
                if (count($return_coins) > 0) {
                    $coins_str = array_map(fn($coin) => number_format($coin->getValue(), 2), $return_coins);
                    $consoleIO->showMessage("Returned coins: " . implode(", ", $coins_str));
                }
            }
        } elseif ($action === 3) {
            $return_coins = $vendingMachine->refundTransaction();
            if (count($return_coins) > 0) {
                $coins_str = array_map(fn($coin) => number_format($coin->getValue(), 2), $return_coins);
                $consoleIO->showMessage("Returned coins: " . implode(", ", $coins_str));
            } else {
                $consoleIO->showMessage("No coins to return.");
            }
        } elseif ($action === 4) {
            $current_balance = $vendingMachine->getCurrentTransaction()->getBalance();
            $consoleIO->showMessage("Current amount inserted: " . number_format($current_balance, 2) . PHP_EOL);
        } elseif ($action === 0) {
            $consoleIO->showMessage("Exiting customer mode." . PHP_EOL);
            if (count($return_coins) > 0) {
                $coins_str = array_map(fn($coin) => number_format($coin->getValue(), 2), $return_coins);
                $consoleIO->showMessage("Returned coins: " . implode(", ", $coins_str));
            }
        }
    } while ($action != 0);

    $vendingMachine->closeTransaction();

    return 0;
}


function loadData($itemRepository, $cashBoxItemRepository): void
{
    // Load initial data if repositories are empty
    if (count($itemRepository->findAll()) === 0) {
        $itemRepository->create(new \App\Domain\Item('WATER', 'Water Bottle', 0.65, 10));
        $itemRepository->create(new \App\Domain\Item('JUICE', 'Juice Bottle', 1.00, 5));
        $itemRepository->create(new \App\Domain\Item('SODA', 'Soda Bottle', 1.50, 8));
    }

    if (count($cashBoxItemRepository->findAll()) === 0) {
        $cashBoxItemRepository->create(new \App\Domain\CashBoxItem(new Coin(0.05), 20));
        $cashBoxItemRepository->create(new \App\Domain\CashBoxItem(new Coin(0.10), 15));
        $cashBoxItemRepository->create(new \App\Domain\CashBoxItem(new Coin(0.25), 10));
        $cashBoxItemRepository->create(new \App\Domain\CashBoxItem(new Coin(1.00), 5));
    }
}

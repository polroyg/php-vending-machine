#!/usr/bin/env php
<?php

declare(strict_types=1);

error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '0');

require __DIR__ . '/../vendor/autoload.php';

use App\Domain\VendingMachine;
use App\Domain\Coin;

set_time_limit(0);
try {
    $params = parseParams($_SERVER['argv']);
    $action = array_pop($params);

    isActionAccepted($action);

    $vendingMachine = initMachine();

    if ($action === "SERVICE") {
        if (count($params) === 0) {
            $params = ["ITEMS", "COINS"];
        }
        handleServiceCommand($vendingMachine, $params);
    } else {
        handleCustomerCommand($vendingMachine, $action, $params);
    }
} catch (\Throwable $th) {
    //throw $th;
    echo ("An error has occurred: " . $th->getMessage() . PHP_EOL);
}


function parseParams(array $argv): array
{
    if (count($argv) <= 1) {
        throw new \InvalidArgumentException("At least one command is required");
    }
    array_shift($argv);
    $parameters = $argv;
    //Llega un argumento separado por comas
    if (count($parameters) === 1) {
        $parameters = explode(", ", $parameters[0]);
    }
    $parameters = array_map(function ($param) {
        return trim($param, ", ");
    }, $parameters);


    return $parameters;
}

function isActionAccepted(string $action): void
{
    $acceptedActions = ['RETURN-COIN', "SERVICE"];
    if (!str_starts_with($action, 'GET-') && !in_array($action, $acceptedActions)) {
        throw new \InvalidArgumentException("Action '$action' is not accepted.");
    }
}

function initMachine(): VendingMachine
{
    $itemRepository = new \App\Infrastructure\Repositories\ItemJsonRepository(
        new \App\Infrastructure\JsonStorage('./data/items.json')
    );
    $cashBoxItemRepository = new \App\Infrastructure\Repositories\CashBoxItemJsonRepository(
        new \App\Infrastructure\JsonStorage('./data/cashbox.json')
    );
    if (count($itemRepository->findAll()) === 0) {
        $itemRepository->create(new \App\Domain\Item('WATER', 'Water Bottle', 0.65, 10));
        $itemRepository->create(new \App\Domain\Item('JUICE', 'Juice Bottle', 1.00, 5));
        $itemRepository->create(new \App\Domain\Item('SODA', 'Soda Bottle', 1.50, 8));
    }

    if (count($cashBoxItemRepository->findAll()) === 0) {
        $cashBoxItemRepository->create(new \App\Domain\CashBoxItem(new \App\Domain\Coin(0.05), 20));
        $cashBoxItemRepository->create(new \App\Domain\CashBoxItem(new \App\Domain\Coin(0.10), 15));
        $cashBoxItemRepository->create(new \App\Domain\CashBoxItem(new \App\Domain\Coin(0.25), 10));
        $cashBoxItemRepository->create(new \App\Domain\CashBoxItem(new \App\Domain\Coin(1.00), 5));
    }
    return new VendingMachine(
        $itemRepository,
        $cashBoxItemRepository
    );
}


function handleServiceCommand(VendingMachine $vendingMachine, array $params): void
{
    foreach ($params as $param) {
        if ($param === "COINS") {
            printCashStatusService($vendingMachine);
        } elseif ($param === "ITEMS") {
            printItemsStatusService($vendingMachine);
        }
        echo "\n";
    }
}

function printCashStatusService(VendingMachine $vendingMachine): void
{
    $cashbox_info = $vendingMachine->getCashBoxStatus();
    foreach ($cashbox_info['coins'] as $coin) {
        echo  "Coin: " . number_format($coin->getCoin()->getValue(), 2) .
            " - Quantity: " . $coin->getQuantity() . "\t";
    }
}

function printItemsStatusService(VendingMachine $vendingMachine): void
{
    $items = $vendingMachine->getAvailableItems();
    foreach ($items as $item) {
        echo   $item->getKey() . " => " .
            $item->getName() . " (" .
            number_format($item->getPrice(), 2) . ") - " .
            $item->getQuantity() . "\t";
    }
}


function handleCustomerCommand(VendingMachine $vendingMachine, string $action, array $coins): void
{
    //Logica de cliente
    $vendingMachine->startTransaction();
    echo " -> ";
    foreach ($coins as $coinValue) {
        $vendingMachine->addCoinToTransaction(new Coin((float)$coinValue));
    }
    if (str_starts_with($action, 'GET-')) {
        $itemCode = str_replace('GET-', '', $action);
        handleBuyItem($vendingMachine, $itemCode);
    } else if ($action === "RETURN-COIN") {
        handleReturnCoin($vendingMachine);
    }
    $vendingMachine->closeTransaction();
}

function handleBuyItem(VendingMachine $vendingMachine, string $itemCode): void
{
    $result = $vendingMachine->buyItem($itemCode);
    echo " " . $result['item']->getName();
    if (count($result['change']) > 0) {
        $changeCoinValues = array_map(fn($coin) => number_format($coin->getValue(), 2), $result['change']);
        echo " " . implode(", ", $changeCoinValues) . PHP_EOL;
    }
}

function handleReturnCoin(VendingMachine $vendingMachine): void
{
    $returnedCoins = $vendingMachine->refundTransaction();
    $returnedCoinValues = array_map(fn($coin) => number_format($coin->getValue(), 2), $returnedCoins);
    echo " " . implode(", ", $returnedCoinValues) . PHP_EOL;
}

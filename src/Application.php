<?php

namespace App;

class Application
{
    private $consoleIO;
    private $vendingMachine;
    private $itemRepository;
    private $cashBoxItemRepository;

    public function __construct()
    {
        $this->consoleIO = new \App\Infrastructure\ConsoleIO();
        $this->itemRepository = new \App\Infrastructure\Repositories\ItemJsonRepository(
            new \App\Infrastructure\JsonStorage('./data/items.json')
        );
        $this->cashBoxItemRepository = new \App\Infrastructure\Repositories\CashBoxItemJsonRepository(
            new \App\Infrastructure\JsonStorage('./data/cashbox.json')
        );
        $this->loadData();
        $this->vendingMachine = new \App\Domain\VendingMachine(
            $this->itemRepository,
            $this->cashBoxItemRepository
        );
    }

    public function run(): void
    {
        set_time_limit(0);
        $this->showStartMessage();
        do {
            $action = $this->getMainAction();
            if ($action === 2) {
                $this->processServiceActions();
            } elseif ($action === 1) {
                $this->processCustomerActions();
            }
        } while ($action != 0);
        $this->consoleIO->showMessage("Thank you for using the vending machine. Goodbye!" . PHP_EOL);
    }

    private function showStartMessage(): void
    {
        $this->consoleIO->showMessage("████████████████████████");
        $this->consoleIO->showMessage("█   VENDING MACHINE    █");
        $this->consoleIO->showMessage("████████████████████████");
        $this->consoleIO->showMessage("");
    }

    private function getMainAction(): int
    {
        $this->consoleIO->showMainMenu();
        $this->consoleIO->showMessageInline("Enter your choice: ");
        $action = (int)fgets(STDIN);
        $this->consoleIO->showMessage("");
        return $action;
    }

    private function processServiceActions(): void
    {
        $this->consoleIO->showServiceMenu();
    }

    private function getNextAction(): int
    {
        $this->consoleIO->showMessage("*****************\n");
        $this->consoleIO->getCustomerMenu();
        $this->consoleIO->showMessageInline("Enter your choice: ");
        $action = (int)fgets(STDIN);
        $this->consoleIO->showMessage("");
        return $action;
    }

    private function processCustomerActions(): void
    {
        if ($this->vendingMachine->getCurrentTransaction() === null) {
            $this->vendingMachine->startTransaction();
        }
        do {
            $action = $this->getNextAction();
            switch ($action) {
                case 1:
                    $this->handleInsertCoin();
                    break;
                case 2:
                    $return_coins = $this->handleBuyItem();
                    break;
                case 3:
                    $return_coins = $this->handleRefund();
                    break;
                case 4:
                    $this->showCurrentBalance();
                    break;
                case 5:
                    $this->handleCloseCurrentTransaction();
                    break;
            }
        } while ($action != 0);

        $this->vendingMachine->closeTransaction();
    }

    private function handleInsertCoin(): void
    {
        $this->consoleIO->showMessage("** Accepted coins [" . implode(", ", $this->vendingMachine->getAcceptedCoins()) . "] **");
        $this->consoleIO->showMessageInline("Insert coin value: ");
        $coinValue = (float)fgets(STDIN);
        try {
            $coin = new \App\Domain\Coin($coinValue);
            $this->vendingMachine->addCoinToTransaction($coin);
            $this->consoleIO->showMessage("Inserted coin: " . number_format($coinValue, 2));
        } catch (\InvalidArgumentException $e) {
            $this->consoleIO->showMessage("Error: " . $e->getMessage());
        } catch (\Exception $e) {
            $this->consoleIO->showMessage("Error: " . $e->getMessage());
        }
    }

    private function handleBuyItem(): void
    {
        $products_list = $this->vendingMachine->getAvailableItems();
        $products_str = array_map(
            fn($item) => $item->getKey() . " => " . $item->getName() . "(" . number_format($item->getPrice(), 2) . ") - " . $item->getQuantity(),
            $products_list
        );
        $this->consoleIO->showMessage("** Available items [" . implode(", ", $products_str) . "] **");
        $this->consoleIO->showMessageInline("Insert item key: ");
        $itemKey = trim(fgets(STDIN));
        try {
            $result = $this->vendingMachine->buyItem($itemKey);
            if (isset($result['item'])) {
                $this->consoleIO->showMessage("Dispensed item: " . $itemKey);
            }
            if (isset($result['change']) && count($result['change']) > 0) {
                $coins_str = array_map(fn($coin) => number_format($coin->getValue(), 2), $result['change']);
                $this->consoleIO->showMessage("Returned change: " . implode(", ", $coins_str));
            }
        } catch (\Exception $e) {
            $this->consoleIO->showMessage("Error: " . $e->getMessage());
        }
    }

    private function handleRefund(): void
    {
        $return_coins = $this->vendingMachine->refundTransaction();
        if (count($return_coins) > 0) {
            $coins_str = array_map(fn($coin) => number_format($coin->getValue(), 2), $return_coins);
            $this->consoleIO->showMessage("Returned coins: " . implode(", ", $coins_str));
        } else {
            $this->consoleIO->showMessage("No coins to return.");
        }
    }

    private function showCurrentBalance(): void
    {
        $current_balance = $this->vendingMachine->getCurrentTranssactionBalance();
        $this->consoleIO->showMessage("Current amount inserted: " . number_format($current_balance, 2) . PHP_EOL);
    }

    private function handleCloseCurrentTransaction(): void
    {
        $return_coins = $this->vendingMachine->refundTransaction();
        if (count($return_coins) > 0) {
            $coins_str = array_map(fn($coin) => number_format($coin->getValue(), 2), $return_coins);
            $this->consoleIO->showMessage("Returned coins: " . implode(", ", $coins_str));
        }
        $this->vendingMachine->closeTransaction();
        $this->consoleIO->showMessage("Transaction closed successfully." . PHP_EOL);
    }


    private function loadData(): void
    {
        // Load initial data if repositories are empty
        if (count($this->itemRepository->findAll()) === 0) {
            $this->itemRepository->create(new \App\Domain\Item('WATER', 'Water Bottle', 0.65, 10));
            $this->itemRepository->create(new \App\Domain\Item('JUICE', 'Juice Bottle', 1.00, 5));
            $this->itemRepository->create(new \App\Domain\Item('SODA', 'Soda Bottle', 1.50, 8));
        }

        if (count($this->cashBoxItemRepository->findAll()) === 0) {
            $this->cashBoxItemRepository->create(new \App\Domain\CashBoxItem(new \App\Domain\Coin(0.05), 20));
            $this->cashBoxItemRepository->create(new \App\Domain\CashBoxItem(new \App\Domain\Coin(0.10), 15));
            $this->cashBoxItemRepository->create(new \App\Domain\CashBoxItem(new \App\Domain\Coin(0.25), 10));
            $this->cashBoxItemRepository->create(new \App\Domain\CashBoxItem(new \App\Domain\Coin(1.00), 5));
        }
    }
}

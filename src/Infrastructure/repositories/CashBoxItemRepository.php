<?php

namespace App\Infrastructure\Repositories;

use App\Domain\CashBoxItem;
use App\Domain\Coin;
use App\Infrastructure\JsonStorage;

//TODO: Crear interficie de repositorios
class CashBoxItemRepository
{
    private JsonStorage $storage;

    public function __construct(JsonStorage $storage)
    {
        $this->storage = $storage;
    }

    public function create(CashBoxItem $item): void
    {
        $exists = $this->findByCoin($item->getCoin());
        if ($exists) {
            throw new \RuntimeException("Item with key {$item->getCoin()->getValue()} already exists");
        }
        $items = $this->findAll();
        $items[] = $item;
        $this->storage->save($this->mapToArray($items));
    }

    public function findAll(): array
    {
        $items = $this->storage->load();
        return $this->mapToCashBoxItems($items);
    }

    public function findByCoin(Coin $coin): ?CashBoxItem
    {
        $items = $this->storage->load();
        $domainItems = $this->mapToCashBoxItems($items);
        return array_values(array_filter($domainItems, fn($item) => $item->getCoin()->equals($coin)))[0] ?? null;
    }

    public function update(CashBoxItem $item): void
    {
        $exists = $this->findByCoin($item->getCoin());
        if (!$exists) {
            throw new \RuntimeException("Item with key {$item->getCoin()->getValue()} not found");
        }
        $items = $this->storage->load();
        $domainItems = $this->mapToCashBoxItems($items);

        $updatedItems = array_map(
            function ($domainItem) use ($item) {
                if ($domainItem->getCoin()->equals($item->getCoin())) {
                    return $item;
                }
                return $domainItem;
            },
            $domainItems
        );
        $this->storage->save($this->mapToArray($updatedItems));
    }

    public function updateAll(array $items): void
    {
        $this->storage->save($this->mapToArray($items));
    }

    public function deleteByCoin(Coin $coin): void
    {
        $exists = $this->findByCoin($coin);
        if (!$exists) {
            throw new \RuntimeException("Item with key {$coin->getValue()} not found");
        }
        $items = $this->storage->load();
        $domainItems = $this->mapToCashBoxItems($items);
        $filteredItems = array_filter(
            $domainItems,
            fn($domainItem) => !$domainItem->getCoin()->equals($coin)
        );
        $this->storage->save($this->mapToArray($filteredItems));
    }

    private function mapToCashBoxItems(array $items): array
    {
        return array_map(fn($item) => new CashBoxItem(
            new Coin($item['value']),
            $item['quantity'] ?? 0,
        ), $items);
    }

    private function mapToArray(array $items): array
    {
        return array_map(function (CashBoxItem $item) {
            return [
                'value' => $item->getCoin()->getValue(),
                'quantity' => $item->getQuantity(),
            ];
        }, $items);
    }
}

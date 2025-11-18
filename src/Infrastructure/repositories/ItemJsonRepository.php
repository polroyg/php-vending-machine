<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Item;
use App\Infrastructure\JsonStorage;

//TODO: Crear interficie de repositorios
class ItemJsonRepository
{
    private JsonStorage $storage;

    public function __construct(JsonStorage $storage)
    {
        $this->storage = $storage;
    }

    public function create(Item $item): void
    {
        $exists = $this->findByKey($item->getKey());
        if ($exists) {
            throw new \RuntimeException("Item with key {$item->getKey()} already exists");
        }
        $items = $this->findAll();
        $items[] = $item;
        $this->storage->save($this->mapToArray($items));
    }

    public function findAll(): array
    {
        $items = $this->storage->load();
        return $this->mapToDomainItems($items);
    }

    public function findByKey(string $key): ?Item
    {
        $items = $this->storage->load();
        $domainItems = $this->mapToDomainItems($items);
        return array_values(array_filter($domainItems, fn($item) => $item->getKey() === $key))[0] ?? null;
    }

    public function update(Item $item): void
    {
        $exists = $this->findByKey($item->getKey());
        if (!$exists) {
            throw new \RuntimeException("Item with key {$item->getKey()} not found");
        }
        $items = $this->storage->load();
        $domainItems = $this->mapToDomainItems($items);

        $updatedItems = array_map(
            function ($domainItem) use ($item) {
                if ($domainItem->getKey() === $item->getKey()) {
                    return $item;
                }
                return $domainItem;
            },
            $domainItems
        );
        $this->storage->save($this->mapToArray($updatedItems));
    }

    public function delete(string $key): void
    {
        $exists = $this->findByKey($key);
        if (!$exists) {
            throw new \RuntimeException("Item with key {$key} not found");
        }
        $items = $this->storage->load();
        $domainItems = $this->mapToDomainItems($items);
        $filteredItems = array_filter($domainItems, fn($domainItem) => $domainItem->getKey() !== $key);
        $this->storage->save($this->mapToArray(array_values($filteredItems)));
    }

    private function mapToDomainItems(array $items): array
    {
        return array_map(fn($item) => new Item(
            $item['key'],
            $item['name'],
            $item['price'] ?? 0,
            $item['quantity'] ?? 0
        ), $items);
    }

    private function mapToArray(array $items): array
    {
        return array_map(function (Item $item) {
            return [
                'key' => $item->getKey(),
                'name' => $item->getName(),
                'price' => $item->getPrice(),
                'quantity' => $item->getQuantity(),
            ];
        }, $items);
    }
}

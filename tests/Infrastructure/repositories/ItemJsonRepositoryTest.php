<?php

namespace Tests\Infrastructure\Repositories;

use PHPUnit\Framework\TestCase;
use App\Infrastructure\Repositories\ItemJsonRepository;
use App\Infrastructure\JsonStorage;
use App\Domain\Item;

class ItemJsonRepositoryTest extends TestCase
{
    private $storageMock;
    private ItemJsonRepository $repository;

    protected function setUp(): void
    {
        $this->storageMock = $this->createMock(JsonStorage::class);
        $this->repository = new ItemJsonRepository($this->storageMock);
    }

    public function testCreateElement()
    {
        $this->storageMock->method('load')->willReturn([]);
        $newItem = new Item('3', 'Item3', 15.00, 3);
        $this->storageMock->expects($this->once())
            ->method('save')
            ->with([$newItem]);

        $this->repository->create($newItem);
    }

    public function testCreateElementExists()
    {
        $existing = [
            ['key' => '1', 'name' => 'Item1', 'price' => 10.50, 'quantity' => 2],
        ];
        $this->storageMock->method('load')->willReturn($existing);

        $newItem = new Item('1', 'Item1', 15.00, 3);

        $this->expectException(\RuntimeException::class);

        $this->repository->create($newItem);
    }

    public function testFindAll()
    {
        $data = [
            ['key' => '1', 'name' => 'Item1', 'price' => 10.50, 'quantity' => 2],
            ['key' => '2', 'name' => 'Item2', 'price' => 20.00, 'quantity' => 5],
        ];
        $this->storageMock->method('load')->willReturn($data);

        $items = $this->repository->findAll();

        $this->assertCount(2, $items);
        $this->assertInstanceOf(Item::class, $items[0]);
        $this->assertEquals('1', $items[0]->getKey());
        $this->assertEquals('Item2', $items[1]->getName());
    }

    public function testFindByKey()
    {
        $data = [
            ['key' => '1', 'name' => 'Item1', 'price' => 10.50, 'quantity' => 2],
        ];
        $this->storageMock->method('load')->willReturn($data);

        $item = $this->repository->findByKey('1');

        $this->assertInstanceOf(Item::class, $item);
        $this->assertEquals('1', $item->getKey());
    }

    public function testFindByKeyNotExists()
    {
        $this->storageMock->method('load')->willReturn([]);

        $item = $this->repository->findByKey('2');

        $this->assertNull($item);
    }

    public function testUpdateSavesUpdatedItem()
    {
        $existing = [
            ['key' => '1', 'name' => 'Item1', 'price' => 10.50, 'quantity' => 2],
        ];
        $this->storageMock->method('load')->willReturn($existing);

        $updatedItem = new Item('1', 'Item1 Modified', 10.50, 5);

        $this->storageMock->expects($this->once())
            ->method('save')
            ->with([$updatedItem]);

        $this->repository->update($updatedItem);
    }

    public function testDeleteRemovesItem()
    {
        $existing = [
            ['key' => 'k1', 'name' => 'Item1', 'price' => 1, 'quantity' => 1],
            ['key' => 'k2', 'name' => 'Item2', 'price' => 2, 'quantity' => 2],
        ];
        $this->storageMock->method('load')->willReturn($existing);

        $this->storageMock->expects($this->once())
            ->method('save')
            ->with($this->callback(function ($items) {
                return count($items) === 1 && $items[0]->getKey() === 'k2';
            }));

        $this->repository->delete('k1');
    }
}

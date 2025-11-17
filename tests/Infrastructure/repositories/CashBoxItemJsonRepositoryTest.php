<?php

namespace Tests\Infrastructure\Repositories;

use PHPUnit\Framework\TestCase;
use App\Infrastructure\Repositories\CashBoxItemRepository;
use App\Infrastructure\JsonStorage;
use App\Domain\CashBoxItem;
use App\Domain\Coin;

class CashBoxItemJsonRepositoryTest extends TestCase
{
    private $storageMock;
    private CashBoxItemRepository $repository;

    protected function setUp(): void
    {
        $this->storageMock = $this->createMock(JsonStorage::class);
        $this->repository = new CashBoxItemRepository($this->storageMock);
    }

    public function testCreateElement()
    {
        $this->storageMock->method('load')->willReturn([]);
        $newItem = new CashBoxItem(new Coin(0.10), 3);
        $this->storageMock->expects($this->once())
            ->method('save')
            ->with([['value' => 0.10, 'quantity' => 3]]);

        $this->repository->create($newItem);
    }

    public function testCreateElementExists()
    {
        $existing = [
            ['value' => 0.05, 'quantity' => 10],
        ];
        $this->storageMock->method('load')->willReturn($existing);

        $newItem = new CashBoxItem(new Coin(0.05), 3);

        $this->expectException(\RuntimeException::class);

        $this->repository->create($newItem);
    }

    public function testFindAll()
    {
        $data = [
            ['value' => 0.05, 'quantity' => 10],
            ['value' => 0.10, 'quantity' => 5],
        ];
        $this->storageMock->method('load')->willReturn($data);

        $items = $this->repository->findAll();

        $this->assertCount(2, $items);
        $this->assertInstanceOf(CashBoxItem::class, $items[0]);
        $this->assertEquals(0.05, $items[0]->getCoin()->getValue());
        $this->assertEquals(5, $items[1]->getQuantity());
    }

    public function testFindByKey()
    {
        $data = [
            ['value' => 0.05, 'quantity' => 10],
        ];
        $this->storageMock->method('load')->willReturn($data);

        $item = $this->repository->findByCoin(new Coin(0.05));

        $this->assertInstanceOf(CashBoxItem::class, $item);
        $this->assertEquals(0.05, $item->getCoin()->getValue());
    }

    public function testFindByKeyNotExists()
    {
        $this->storageMock->method('load')->willReturn([]);

        $item = $this->repository->findByCoin(new Coin(1));

        $this->assertNull($item);
    }

    public function testUpdateSavesUpdatedItem()
    {
        $existing = [
            ['value' => 0.25, 'quantity' => 10],
        ];
        $this->storageMock->method('load')->willReturn($existing);

        $updatedItem = new CashBoxItem(new Coin(0.25), 5);
        $this->storageMock->expects($this->once())
            ->method('save')
            ->with([['value' => 0.25, 'quantity' => 5]]);

        $this->repository->update($updatedItem);
        $items = $this->repository->findAll();
        $this->assertCount(1, $items);
    }

    public function testDeleteRemoveItem()
    {
        $existing = [
            ['value' => 0.25, 'quantity' => 10],
            ['value' => 0.10, 'quantity' => 5],
        ];
        $this->storageMock->method('load')->willReturn($existing);
        $this->storageMock->expects($this->once())
            ->method('save')
            ->with($this->callback(function ($items) {
                $values = array_values($items);
                return count($items) === 1 &&
                       isset($values[0]['value']) &&
                       $values[0]['value'] === 0.10 &&
                       isset($values[0]['quantity']) &&
                       $values[0]['quantity'] === 5;
            }));
         $this->repository->deleteByCoin(new Coin(0.25));
    }
}

<?php

namespace App\Modules\Inventory\Application\UseCases\RegisterStockOutput;

use App\Modules\Catalog\Domain\Exceptions\ProductNotFoundException;
use App\Modules\Catalog\Domain\Repositories\ProductRepository;
use App\Modules\Catalog\Domain\ValueObjects\ProductId as CatalogProductId;
use App\Modules\Inventory\Application\UseCases\RegisterStockOutput\Dtos\RegisterStockOutputInput;
use App\Modules\Inventory\Application\UseCases\RegisterStockOutput\Dtos\RegisterStockOutputOutput;
use App\Modules\Inventory\Domain\Exceptions\InsufficientStockException;
use App\Modules\Inventory\Domain\Factories\StockMovementFactory;
use App\Modules\Inventory\Domain\Repositories\InventoryItemRepository;
use App\Modules\Inventory\Domain\Repositories\StockMovementRepository;
use App\Modules\Inventory\Domain\ValueObjects\MovementQuantity;
use App\Modules\Inventory\Domain\ValueObjects\MovementReason;
use App\Modules\Inventory\Domain\ValueObjects\StockMovementId;
use App\Modules\Inventory\Domain\ValueObjects\StockOutputType;
use App\Modules\Inventory\Domain\ValueObjects\StockProductId;
use App\Modules\Shared\Application\Contracts\InputDto;
use App\Modules\Shared\Application\Contracts\OutputDto;
use App\Modules\Shared\Application\Contracts\TransactionManager;
use App\Modules\Shared\Application\Contracts\UseCase;
use App\Modules\Tenant\Domain\ValueObjects\TenantId;
use DateTimeImmutable;
use Illuminate\Support\Str;

/**
 * @implements UseCase<RegisterStockOutputInput, RegisterStockOutputOutput>
 */
final readonly class RegisterStockOutputUseCase implements UseCase
{
    public function __construct(
        private ProductRepository $products,
        private InventoryItemRepository $inventoryItems,
        private StockMovementRepository $stockMovements,
        private StockMovementFactory $stockMovementFactory,
        private TransactionManager $transactionManager,
    ) {}

    public function execute(InputDto $input): OutputDto
    {
        assert($input instanceof RegisterStockOutputInput);

        return $this->transactionManager->run(function () use ($input): RegisterStockOutputOutput {
            $tenantId = new TenantId($input->tenantId);
            $catalogProductId = new CatalogProductId($input->productId);
            $stockProductId = new StockProductId($input->productId);

            if ($this->products->findById($tenantId, $catalogProductId) === null) {
                throw new ProductNotFoundException;
            }

            $inventoryItem = $this->inventoryItems->findByProductId($tenantId, $stockProductId);

            if ($inventoryItem === null) {
                throw new InsufficientStockException;
            }

            $quantity = new MovementQuantity($input->quantity);
            $inventoryItem->decrease($quantity);

            $movement = $this->stockMovementFactory->createOutput(
                id: new StockMovementId((string) Str::uuid()),
                tenantId: $tenantId,
                productId: $stockProductId,
                userId: $input->userId,
                type: new StockOutputType($input->type),
                quantity: $quantity,
                reason: new MovementReason($input->reason),
                note: $input->note,
                occurredAt: new DateTimeImmutable,
            );

            $this->inventoryItems->update($inventoryItem);
            $this->stockMovements->save($movement);

            return new RegisterStockOutputOutput(
                movementId: $movement->id()->value,
                inventoryItemId: $inventoryItem->id()->value,
                tenantId: $tenantId->value,
                productId: $stockProductId->value,
                type: $movement->type()->value,
                quantity: $movement->quantity()->value,
                currentStock: $inventoryItem->currentStock()->value,
                reason: $movement->reason()->value,
                note: $movement->note(),
                occurredAt: $movement->occurredAt()->format(DATE_ATOM),
            );
        });
    }
}

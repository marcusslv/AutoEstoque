<?php

namespace Tests\Unit\Workshop;

use App\Modules\Catalog\Domain\ValueObjects\ProductId;
use App\Modules\Shared\Domain\Exceptions\DomainValidationException;
use App\Modules\Tenant\Domain\ValueObjects\TenantId;
use App\Modules\Workshop\Domain\Entities\ServiceOrderItem;
use App\Modules\Workshop\Domain\Factories\ServiceOrderItemFactory;
use App\Modules\Workshop\Domain\ValueObjects\ServiceOrderId;
use App\Modules\Workshop\Domain\ValueObjects\ServiceOrderItemId;
use PHPUnit\Framework\TestCase;

class ServiceOrderItemTest extends TestCase
{
    public function test_it_creates_a_valid_service_order_item(): void
    {
        $item = $this->makeItem(quantity: 2);

        $this->assertSame('018f95f2-0f08-7f85-9b31-2d833a1a2f43', $item->serviceOrderId()->value);
        $this->assertSame('018f95f2-0f08-7f85-9b31-2d833a1a2f44', $item->productId()->value);
        $this->assertSame('018f95f2-0f08-7f85-9b31-2d833a1a2f45', $item->addedByUserId());
        $this->assertSame(2, $item->quantity());
    }

    public function test_it_rejects_invalid_item_using_domain_validator(): void
    {
        try {
            $this->makeItem(addedByUserId: ' ', quantity: 0);
            $this->fail('Expected domain validation exception.');
        } catch (DomainValidationException $exception) {
            $this->assertSame([
                [
                    'field' => 'added_by_user_id',
                    'message' => 'Added by user id is required.',
                    'code' => 'service_order_item.added_by_user_required',
                ],
                [
                    'field' => 'quantity',
                    'message' => 'Quantity must be greater than zero.',
                    'code' => 'service_order_item.quantity_invalid',
                ],
            ], $exception->errors());
        }
    }

    private function makeItem(
        string $addedByUserId = '018f95f2-0f08-7f85-9b31-2d833a1a2f45',
        int $quantity = 1,
    ): ServiceOrderItem {
        return (new ServiceOrderItemFactory)->create(
            id: new ServiceOrderItemId('018f95f2-0f08-7f85-9b31-2d833a1a2f41'),
            tenantId: new TenantId('018f95f2-0f08-7f85-9b31-2d833a1a2f42'),
            serviceOrderId: new ServiceOrderId('018f95f2-0f08-7f85-9b31-2d833a1a2f43'),
            productId: new ProductId('018f95f2-0f08-7f85-9b31-2d833a1a2f44'),
            addedByUserId: $addedByUserId,
            quantity: $quantity,
        );
    }
}

<?php

namespace App\Modules\Catalog\Domain\Entities;

use App\Modules\Catalog\Domain\Validators\ProductValidator;
use App\Modules\Catalog\Domain\ValueObjects\Barcode;
use App\Modules\Catalog\Domain\ValueObjects\Money;
use App\Modules\Catalog\Domain\ValueObjects\ProductId;
use App\Modules\Catalog\Domain\ValueObjects\Sku;
use App\Modules\Shared\Domain\Entities\Entity;
use App\Modules\Tenant\Domain\ValueObjects\TenantId;

final class Product extends Entity
{
    public function __construct(
        private readonly ProductId $id,
        private readonly TenantId $tenantId,
        private readonly string $name,
        private readonly Sku $sku,
        private readonly Barcode $barcode,
        private readonly ?string $category,
        private readonly ?string $brand,
        private readonly ?string $supplier,
        private readonly int $minimumStock,
        private readonly Money $cost,
    ) {
        parent::__construct();

        ProductValidator::validate($this);

        $this->throwIfNotificationHasErrors();
    }

    public function id(): ProductId
    {
        return $this->id;
    }

    public function tenantId(): TenantId
    {
        return $this->tenantId;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function sku(): Sku
    {
        return $this->sku;
    }

    public function barcode(): Barcode
    {
        return $this->barcode;
    }

    public function category(): ?string
    {
        return $this->category;
    }

    public function brand(): ?string
    {
        return $this->brand;
    }

    public function supplier(): ?string
    {
        return $this->supplier;
    }

    public function minimumStock(): int
    {
        return $this->minimumStock;
    }

    public function cost(): Money
    {
        return $this->cost;
    }
}

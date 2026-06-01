<?php

namespace Tests\Unit\Catalog;

use App\Modules\Catalog\Domain\Exceptions\InvalidBarcodeException;
use App\Modules\Catalog\Domain\Exceptions\InvalidMoneyException;
use App\Modules\Catalog\Domain\Exceptions\InvalidProductIdException;
use App\Modules\Catalog\Domain\Exceptions\InvalidSkuException;
use App\Modules\Catalog\Domain\ValueObjects\Barcode;
use App\Modules\Catalog\Domain\ValueObjects\Money;
use App\Modules\Catalog\Domain\ValueObjects\ProductId;
use App\Modules\Catalog\Domain\ValueObjects\Sku;
use PHPUnit\Framework\TestCase;

class ProductValueObjectsTest extends TestCase
{
    public function test_sku_normalizes_value_to_uppercase(): void
    {
        $sku = new Sku(' fo-001 ');

        $this->assertSame('FO-001', $sku->value);
    }

    public function test_sku_rejects_empty_value(): void
    {
        $this->expectException(InvalidSkuException::class);

        new Sku('   ');
    }

    public function test_barcode_allows_null_value(): void
    {
        $barcode = new Barcode(null);

        $this->assertNull($barcode->value);
    }

    public function test_barcode_trims_value(): void
    {
        $barcode = new Barcode(' 7891234567890 ');

        $this->assertSame('7891234567890', $barcode->value);
    }

    public function test_barcode_rejects_empty_value_when_provided(): void
    {
        $this->expectException(InvalidBarcodeException::class);

        new Barcode('   ');
    }

    public function test_money_accepts_zero_amount(): void
    {
        $money = new Money(0);

        $this->assertSame(0, $money->amountInCents);
        $this->assertSame('BRL', $money->currency);
    }

    public function test_money_rejects_negative_amount(): void
    {
        $this->expectException(InvalidMoneyException::class);

        new Money(-1);
    }

    public function test_money_rejects_invalid_currency(): void
    {
        $this->expectException(InvalidMoneyException::class);

        new Money(100, 'REAL');
    }

    public function test_product_id_accepts_uuid(): void
    {
        $productId = new ProductId('018f95f2-0f08-7f85-9b31-2d833a1a2f41');

        $this->assertSame('018f95f2-0f08-7f85-9b31-2d833a1a2f41', $productId->value);
    }

    public function test_product_id_rejects_invalid_uuid(): void
    {
        $this->expectException(InvalidProductIdException::class);

        new ProductId('invalid');
    }
}

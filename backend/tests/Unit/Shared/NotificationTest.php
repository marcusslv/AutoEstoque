<?php

namespace Tests\Unit\Shared;

use App\Modules\Shared\Domain\Notifications\Notification;
use PHPUnit\Framework\TestCase;

class NotificationTest extends TestCase
{
    public function test_it_starts_without_errors(): void
    {
        $notification = new Notification;

        $this->assertFalse($notification->hasErrors());
        $this->assertSame([], $notification->errors());
        $this->assertSame([], $notification->toArray());
    }

    public function test_it_collects_errors(): void
    {
        $notification = new Notification;

        $notification->add(
            field: 'name',
            message: 'Product name is required.',
            code: 'product.name_required',
        );

        $this->assertTrue($notification->hasErrors());
        $this->assertCount(1, $notification->errors());
        $this->assertSame([
            [
                'field' => 'name',
                'message' => 'Product name is required.',
                'code' => 'product.name_required',
            ],
        ], $notification->toArray());
    }
}

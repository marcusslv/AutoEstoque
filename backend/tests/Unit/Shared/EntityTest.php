<?php

namespace Tests\Unit\Shared;

use App\Modules\Shared\Domain\Entities\Entity;
use App\Modules\Shared\Domain\Exceptions\DomainValidationException;
use PHPUnit\Framework\TestCase;

class EntityTest extends TestCase
{
    public function test_it_has_a_notification_attribute(): void
    {
        $entity = new FakeEntity;

        $this->assertFalse($entity->notification()->hasErrors());
    }

    public function test_it_throws_domain_validation_exception_when_notification_has_errors(): void
    {
        $entity = new FakeEntity;

        $entity->notification()->add(
            field: 'field',
            message: 'Invalid field.',
            code: 'field.invalid',
        );

        try {
            $entity->throwIfInvalid();
            $this->fail('Expected domain validation exception.');
        } catch (DomainValidationException $exception) {
            $this->assertSame([
                [
                    'field' => 'field',
                    'message' => 'Invalid field.',
                    'code' => 'field.invalid',
                ],
            ], $exception->errors());
        }
    }
}

final class FakeEntity extends Entity
{
    public function throwIfInvalid(): void
    {
        $this->throwIfNotificationHasErrors();
    }
}

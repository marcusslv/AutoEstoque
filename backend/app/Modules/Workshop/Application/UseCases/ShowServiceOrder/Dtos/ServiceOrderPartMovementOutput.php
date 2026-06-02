<?php

namespace App\Modules\Workshop\Application\UseCases\ShowServiceOrder\Dtos;

final readonly class ServiceOrderPartMovementOutput
{
    public function __construct(
        public string $id,
        public string $direction,
        public string $type,
        public int $quantity,
        public string $reason,
        public ?string $note,
        public string $occurredAt,
    ) {}
}

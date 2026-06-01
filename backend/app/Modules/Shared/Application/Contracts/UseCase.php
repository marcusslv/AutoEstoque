<?php

namespace App\Modules\Shared\Application\Contracts;

/**
 * @template TInput of InputDto
 * @template TOutput of OutputDto
 */
interface UseCase
{
    /**
     * @param  TInput  $input
     * @return TOutput
     */
    public function execute(InputDto $input): OutputDto;
}

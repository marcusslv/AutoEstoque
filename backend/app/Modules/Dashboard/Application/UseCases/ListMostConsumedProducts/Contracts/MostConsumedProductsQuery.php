<?php

namespace App\Modules\Dashboard\Application\UseCases\ListMostConsumedProducts\Contracts;

use App\Modules\Dashboard\Application\UseCases\ListMostConsumedProducts\Dtos\ListMostConsumedProductsInput;
use App\Modules\Dashboard\Application\UseCases\ListMostConsumedProducts\Dtos\MostConsumedProductOutput;

interface MostConsumedProductsQuery
{
    /**
     * @return array<int, MostConsumedProductOutput>
     */
    public function search(ListMostConsumedProductsInput $input): array;
}

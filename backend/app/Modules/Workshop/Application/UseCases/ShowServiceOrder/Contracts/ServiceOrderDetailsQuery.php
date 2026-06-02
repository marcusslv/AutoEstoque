<?php

namespace App\Modules\Workshop\Application\UseCases\ShowServiceOrder\Contracts;

use App\Modules\Workshop\Application\UseCases\ShowServiceOrder\Dtos\ShowServiceOrderInput;
use App\Modules\Workshop\Application\UseCases\ShowServiceOrder\Dtos\ShowServiceOrderOutput;

interface ServiceOrderDetailsQuery
{
    public function find(ShowServiceOrderInput $input): ?ShowServiceOrderOutput;
}

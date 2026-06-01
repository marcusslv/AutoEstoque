<?php

namespace App\Modules\Shared\Interfaces\Http\Presenters;

use App\Modules\Shared\Application\Contracts\OutputDto;
use Illuminate\Http\JsonResponse;

interface JsonPresenter
{
    public function present(OutputDto $output): JsonResponse;
}

<?php

namespace App\Modules\Dashboard\Application\UseCases\ViewDashboard\Contracts;

use App\Modules\Dashboard\Application\UseCases\ViewDashboard\Dtos\ViewDashboardInput;
use App\Modules\Dashboard\Application\UseCases\ViewDashboard\Dtos\ViewDashboardOutput;

interface DashboardQuery
{
    public function get(ViewDashboardInput $input): ViewDashboardOutput;
}

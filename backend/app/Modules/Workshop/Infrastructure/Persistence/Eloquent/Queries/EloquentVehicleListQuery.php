<?php

namespace App\Modules\Workshop\Infrastructure\Persistence\Eloquent\Queries;

use App\Modules\Workshop\Application\UseCases\ListVehicles\Contracts\VehicleListQuery;
use App\Modules\Workshop\Application\UseCases\ListVehicles\Dtos\ListVehiclesInput;
use App\Modules\Workshop\Application\UseCases\ListVehicles\Dtos\VehicleOutput;
use App\Modules\Workshop\Infrastructure\Persistence\Eloquent\Models\VehicleModel;
use Carbon\CarbonImmutable;

final class EloquentVehicleListQuery implements VehicleListQuery
{
    public function search(ListVehiclesInput $input): array
    {
        $query = VehicleModel::query()
            ->where('tenant_id', $input->tenantId);

        if ($input->term !== null && trim($input->term) !== '') {
            $term = mb_strtolower(trim($input->term));
            $likeTerm = "%{$term}%";

            $query->where(function ($query) use ($likeTerm): void {
                $query
                    ->whereRaw('LOWER(plate) LIKE ?', [$likeTerm])
                    ->orWhereRaw('LOWER(brand) LIKE ?', [$likeTerm])
                    ->orWhereRaw('LOWER(model) LIKE ?', [$likeTerm])
                    ->orWhereRaw('LOWER(owner_name) LIKE ?', [$likeTerm])
                    ->orWhereRaw('LOWER(owner_phone) LIKE ?', [$likeTerm]);
            });
        }

        return $query
            ->orderBy('plate')
            ->limit($input->limit)
            ->get()
            ->map(fn (VehicleModel $vehicle): VehicleOutput => new VehicleOutput(
                id: (string) $vehicle->getAttribute('id'),
                tenantId: (string) $vehicle->getAttribute('tenant_id'),
                plate: (string) $vehicle->getAttribute('plate'),
                brand: (string) $vehicle->getAttribute('brand'),
                model: (string) $vehicle->getAttribute('model'),
                year: (int) $vehicle->getAttribute('year'),
                ownerName: (string) $vehicle->getAttribute('owner_name'),
                ownerPhone: (string) $vehicle->getAttribute('owner_phone'),
                createdAt: CarbonImmutable::parse($vehicle->getAttribute('created_at'))->toAtomString(),
            ))
            ->all();
    }
}

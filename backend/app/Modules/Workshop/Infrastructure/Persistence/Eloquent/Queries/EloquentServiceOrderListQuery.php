<?php

namespace App\Modules\Workshop\Infrastructure\Persistence\Eloquent\Queries;

use App\Modules\Workshop\Application\UseCases\ListServiceOrders\Contracts\ServiceOrderListQuery;
use App\Modules\Workshop\Application\UseCases\ListServiceOrders\Dtos\ListServiceOrdersInput;
use App\Modules\Workshop\Application\UseCases\ListServiceOrders\Dtos\ServiceOrderListItemOutput;
use App\Modules\Workshop\Application\UseCases\ListServiceOrders\Dtos\ServiceOrderListVehicleOutput;
use App\Modules\Workshop\Infrastructure\Persistence\Eloquent\Models\ServiceOrderModel;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

final class EloquentServiceOrderListQuery implements ServiceOrderListQuery
{
    public function search(ListServiceOrdersInput $input): array
    {
        $query = ServiceOrderModel::query()
            ->select([
                'service_orders.id',
                'service_orders.tenant_id',
                'service_orders.customer_name',
                'service_orders.services_description',
                'service_orders.observations',
                'service_orders.status',
                'service_orders.opened_at',
                'service_orders.finished_at',
                'vehicles.id as vehicle_id',
                'vehicles.plate as vehicle_plate',
                'vehicles.brand as vehicle_brand',
                'vehicles.model as vehicle_model',
                'vehicles.owner_name as vehicle_owner_name',
                DB::raw('COUNT(service_order_items.id) as parts_total'),
            ])
            ->join('vehicles', 'vehicles.id', '=', 'service_orders.vehicle_id')
            ->leftJoin('service_order_items', function ($join): void {
                $join
                    ->on('service_order_items.service_order_id', '=', 'service_orders.id')
                    ->on('service_order_items.tenant_id', '=', 'service_orders.tenant_id');
            })
            ->where('service_orders.tenant_id', $input->tenantId)
            ->groupBy([
                'service_orders.id',
                'service_orders.tenant_id',
                'service_orders.customer_name',
                'service_orders.services_description',
                'service_orders.observations',
                'service_orders.status',
                'service_orders.opened_at',
                'service_orders.finished_at',
                'vehicles.id',
                'vehicles.plate',
                'vehicles.brand',
                'vehicles.model',
                'vehicles.owner_name',
            ]);

        if ($input->status !== null) {
            $query->where('service_orders.status', $input->status);
        }

        if ($input->term !== null && trim($input->term) !== '') {
            $term = mb_strtolower(trim($input->term));
            $likeTerm = "%{$term}%";

            $query->where(function ($query) use ($likeTerm): void {
                $query
                    ->whereRaw('LOWER(service_orders.customer_name) LIKE ?', [$likeTerm])
                    ->orWhereRaw('LOWER(service_orders.services_description) LIKE ?', [$likeTerm])
                    ->orWhereRaw('LOWER(vehicles.plate) LIKE ?', [$likeTerm])
                    ->orWhereRaw('LOWER(vehicles.owner_name) LIKE ?', [$likeTerm]);
            });
        }

        if ($input->openedFrom !== null) {
            $query->where('service_orders.opened_at', '>=', CarbonImmutable::parse($input->openedFrom)->startOfDay());
        }

        if ($input->openedTo !== null) {
            $query->where('service_orders.opened_at', '<=', CarbonImmutable::parse($input->openedTo)->endOfDay());
        }

        return $query
            ->orderByDesc('service_orders.opened_at')
            ->orderByDesc('service_orders.created_at')
            ->limit($input->limit)
            ->get()
            ->map(fn (ServiceOrderModel $serviceOrder): ServiceOrderListItemOutput => new ServiceOrderListItemOutput(
                id: (string) $serviceOrder->getAttribute('id'),
                tenantId: (string) $serviceOrder->getAttribute('tenant_id'),
                customerName: (string) $serviceOrder->getAttribute('customer_name'),
                servicesDescription: (string) $serviceOrder->getAttribute('services_description'),
                observations: $serviceOrder->getAttribute('observations') === null
                    ? null
                    : (string) $serviceOrder->getAttribute('observations'),
                status: (string) $serviceOrder->getAttribute('status'),
                openedAt: CarbonImmutable::parse($serviceOrder->getAttribute('opened_at'))->toAtomString(),
                finishedAt: $serviceOrder->getAttribute('finished_at') === null
                    ? null
                    : CarbonImmutable::parse($serviceOrder->getAttribute('finished_at'))->toAtomString(),
                vehicle: new ServiceOrderListVehicleOutput(
                    id: (string) $serviceOrder->getAttribute('vehicle_id'),
                    plate: (string) $serviceOrder->getAttribute('vehicle_plate'),
                    brand: (string) $serviceOrder->getAttribute('vehicle_brand'),
                    model: (string) $serviceOrder->getAttribute('vehicle_model'),
                    ownerName: (string) $serviceOrder->getAttribute('vehicle_owner_name'),
                ),
                partsTotal: (int) $serviceOrder->getAttribute('parts_total'),
            ))
            ->all();
    }
}

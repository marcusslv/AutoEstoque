<?php

namespace App\Modules\Workshop\Infrastructure\Persistence\Eloquent\Queries;

use App\Modules\Workshop\Application\UseCases\ShowServiceOrder\Contracts\ServiceOrderDetailsQuery;
use App\Modules\Workshop\Application\UseCases\ShowServiceOrder\Dtos\ServiceOrderPartOutput;
use App\Modules\Workshop\Application\UseCases\ShowServiceOrder\Dtos\ServiceOrderVehicleOutput;
use App\Modules\Workshop\Application\UseCases\ShowServiceOrder\Dtos\ShowServiceOrderInput;
use App\Modules\Workshop\Application\UseCases\ShowServiceOrder\Dtos\ShowServiceOrderOutput;
use App\Modules\Workshop\Infrastructure\Persistence\Eloquent\Models\ServiceOrderItemModel;
use App\Modules\Workshop\Infrastructure\Persistence\Eloquent\Models\ServiceOrderModel;
use Carbon\CarbonImmutable;

final class EloquentServiceOrderDetailsQuery implements ServiceOrderDetailsQuery
{
    public function find(ShowServiceOrderInput $input): ?ShowServiceOrderOutput
    {
        $serviceOrder = ServiceOrderModel::query()
            ->select([
                'service_orders.id',
                'service_orders.tenant_id',
                'service_orders.created_by_user_id',
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
                'vehicles.year as vehicle_year',
                'vehicles.owner_name as vehicle_owner_name',
                'vehicles.owner_phone as vehicle_owner_phone',
            ])
            ->join('vehicles', 'vehicles.id', '=', 'service_orders.vehicle_id')
            ->where('service_orders.tenant_id', $input->tenantId)
            ->where('service_orders.id', $input->serviceOrderId)
            ->first();

        if (! $serviceOrder instanceof ServiceOrderModel) {
            return null;
        }

        return new ShowServiceOrderOutput(
            id: (string) $serviceOrder->getAttribute('id'),
            tenantId: (string) $serviceOrder->getAttribute('tenant_id'),
            createdByUserId: (string) $serviceOrder->getAttribute('created_by_user_id'),
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
            vehicle: new ServiceOrderVehicleOutput(
                id: (string) $serviceOrder->getAttribute('vehicle_id'),
                plate: (string) $serviceOrder->getAttribute('vehicle_plate'),
                brand: (string) $serviceOrder->getAttribute('vehicle_brand'),
                model: (string) $serviceOrder->getAttribute('vehicle_model'),
                year: (int) $serviceOrder->getAttribute('vehicle_year'),
                ownerName: (string) $serviceOrder->getAttribute('vehicle_owner_name'),
                ownerPhone: (string) $serviceOrder->getAttribute('vehicle_owner_phone'),
            ),
            parts: $this->parts($input),
        );
    }

    /**
     * @return array<int, ServiceOrderPartOutput>
     */
    private function parts(ShowServiceOrderInput $input): array
    {
        return ServiceOrderItemModel::query()
            ->select([
                'service_order_items.id',
                'service_order_items.product_id',
                'products.name as product_name',
                'products.sku as product_sku',
                'service_order_items.added_by_user_id',
                'service_order_items.quantity',
                'service_order_items.created_at',
            ])
            ->join('products', 'products.id', '=', 'service_order_items.product_id')
            ->where('service_order_items.tenant_id', $input->tenantId)
            ->where('service_order_items.service_order_id', $input->serviceOrderId)
            ->orderBy('service_order_items.created_at')
            ->get()
            ->map(fn (ServiceOrderItemModel $item): ServiceOrderPartOutput => new ServiceOrderPartOutput(
                id: (string) $item->getAttribute('id'),
                productId: (string) $item->getAttribute('product_id'),
                productName: (string) $item->getAttribute('product_name'),
                productSku: (string) $item->getAttribute('product_sku'),
                addedByUserId: (string) $item->getAttribute('added_by_user_id'),
                quantity: (int) $item->getAttribute('quantity'),
                createdAt: CarbonImmutable::parse($item->getAttribute('created_at'))->toAtomString(),
            ))
            ->all();
    }
}

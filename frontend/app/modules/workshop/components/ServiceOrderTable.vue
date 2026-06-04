<script setup lang="ts">
import type { DataTableColumn } from '~/components/ui/organisms/DataTable.vue'
import { formatDateTime } from '~/shared/utils/format'
import ServiceOrderStatusBadge from './ServiceOrderStatusBadge.vue'
import type { ServiceOrderListItem } from '../types/serviceOrder'

const props = defineProps<{
  serviceOrders: ServiceOrderListItem[]
}>()

const columns: DataTableColumn[] = [
  { key: 'customer', label: 'Cliente' },
  { key: 'vehicle', label: 'Veiculo' },
  { key: 'status', label: 'Status' },
  { key: 'partsTotal', label: 'Pecas', align: 'right' },
  { key: 'openedAt', label: 'Abertura', align: 'right' },
  { key: 'actions', label: '', align: 'right' },
]

const rows = computed(() => {
  return props.serviceOrders.map((serviceOrder) => ({
    id: serviceOrder.id,
    customer: serviceOrder.customerName,
    description: serviceOrder.servicesDescription,
    vehicle: `${serviceOrder.vehicle.plate} - ${serviceOrder.vehicle.brand} ${serviceOrder.vehicle.model}`,
    status: serviceOrder.status,
    partsTotal: serviceOrder.partsTotal,
    openedAt: formatDateTime(serviceOrder.openedAt),
  }))
})
</script>

<template>
  <DataTable
    :columns="columns"
    :rows="rows"
    empty-title="Nenhuma ordem encontrada"
    empty-description="Crie uma ordem de servico ou ajuste os filtros para continuar."
  >
    <template #cell-customer="{ row, value }">
      <div class="min-w-0">
        <p class="truncate font-medium">
          {{ value }}
        </p>
        <p class="truncate text-xs text-muted-foreground">
          {{ row.description }}
        </p>
      </div>
    </template>

    <template #cell-status="{ value }">
      <ServiceOrderStatusBadge :status="value as ServiceOrderListItem['status']" />
    </template>

    <template #cell-actions="{ row }">
      <NuxtLink :to="`/service-orders/${row.id}`">
        <AppButton size="sm" variant="secondary">
          Detalhar
        </AppButton>
      </NuxtLink>
    </template>
  </DataTable>
</template>

<script setup lang="ts">
import type { DataTableColumn } from '~/components/ui/organisms/DataTable.vue'
import MovementDirectionBadge from './MovementDirectionBadge.vue'
import MovementOriginLink from './MovementOriginLink.vue'
import type { StockMovement, StockMovementDirection, StockMovementServiceOrder, StockMovementType } from '../types/movement'

const props = defineProps<{
  movements: StockMovement[]
}>()

const columns: DataTableColumn[] = [
  { key: 'product', label: 'Produto' },
  { key: 'direction', label: 'Direcao' },
  { key: 'type', label: 'Tipo' },
  { key: 'quantity', label: 'Qtd.', align: 'right' },
  { key: 'reason', label: 'Motivo' },
  { key: 'origin', label: 'Origem' },
  { key: 'occurredAt', label: 'Data' },
]

const typeLabels: Record<StockMovementType, string> = {
  purchase: 'Compra',
  return: 'Devolucao',
  service_consumption: 'Consumo em servico',
  loss: 'Perda',
  breakage: 'Quebra',
  manual_adjustment: 'Ajuste manual',
}

const formatDate = (value: string) => {
  return new Intl.DateTimeFormat('pt-BR', {
    dateStyle: 'short',
    timeStyle: 'short',
  }).format(new Date(value))
}

const rows = computed(() => {
  return props.movements.map((movement) => ({
    product: movement.product.name,
    sku: movement.product.sku,
    direction: movement.direction,
    type: typeLabels[movement.type] ?? movement.type,
    quantity: movement.quantity,
    reason: movement.reason,
    note: movement.note,
    origin: movement.serviceOrder,
    occurredAt: formatDate(movement.occurredAt),
  }))
})
</script>

<template>
  <DataTable
    :columns="columns"
    :rows="rows"
    empty-title="Nenhuma movimentacao encontrada"
    empty-description="Ajuste os filtros ou registre uma movimentacao para ver o historico."
  >
    <template #cell-product="{ row, value }">
      <div class="min-w-0">
        <p class="truncate font-medium">
          {{ value }}
        </p>
        <p class="truncate text-xs text-muted-foreground">
          SKU {{ row.sku }}
        </p>
      </div>
    </template>

    <template #cell-direction="{ value }">
      <MovementDirectionBadge :direction="value as StockMovementDirection" />
    </template>

    <template #cell-quantity="{ row, value }">
      <span
        class="font-medium"
        :class="(row.direction as StockMovementDirection) === 'output' ? 'text-destructive' : 'text-emerald-700'"
      >
        {{ (row.direction as StockMovementDirection) === 'output' ? '-' : '+' }}{{ value }}
      </span>
    </template>

    <template #cell-reason="{ row, value }">
      <div class="max-w-xs">
        <p class="truncate font-medium">
          {{ value }}
        </p>
        <p v-if="row.note" class="truncate text-xs text-muted-foreground">
          {{ row.note }}
        </p>
      </div>
    </template>

    <template #cell-origin="{ value }">
      <MovementOriginLink :service-order="value as StockMovementServiceOrder | null" />
    </template>
  </DataTable>
</template>

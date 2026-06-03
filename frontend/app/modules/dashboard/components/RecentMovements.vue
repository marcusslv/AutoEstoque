<script setup lang="ts">
import type { DataTableColumn } from '~/components/ui/organisms/DataTable.vue'
import type { DashboardRecentMovement } from '../types/dashboard'

const props = defineProps<{
  movements: DashboardRecentMovement[]
}>()

const columns: DataTableColumn[] = [
  { key: 'product', label: 'Produto' },
  { key: 'direction', label: 'Tipo' },
  { key: 'quantity', label: 'Qtd.', align: 'right' },
  { key: 'reason', label: 'Motivo' },
  { key: 'occurredAt', label: 'Horario', align: 'right' },
]

const formatDateTime = (value: string) => {
  return new Intl.DateTimeFormat('pt-BR', {
    hour: '2-digit',
    minute: '2-digit',
  }).format(new Date(value))
}

const movementTypeLabel = (direction: DashboardRecentMovement['direction']) => {
  return direction === 'input' ? 'Entrada' : 'Saida'
}

const rows = computed(() => {
  return props.movements.map((movement) => ({
    product: `${movement.product.name} (${movement.product.sku})`,
    direction: movement.direction,
    quantity: movement.quantity,
    reason: movement.reason,
    occurredAt: formatDateTime(movement.occurredAt),
  }))
})
</script>

<template>
  <ListPageTemplate
    title="Movimentacoes recentes"
    description="Ultimos eventos operacionais registrados no estoque."
  >
    <DataTable
      :columns="columns"
      :rows="rows"
      empty-title="Nenhuma movimentacao hoje"
      empty-description="As entradas e saidas do dia aparecem aqui."
    >
      <template #cell-direction="{ value }">
        <StatusBadge
          :label="movementTypeLabel(value as DashboardRecentMovement['direction'])"
          :tone="value === 'input' ? 'success' : 'warning'"
        />
      </template>
    </DataTable>
  </ListPageTemplate>
</template>

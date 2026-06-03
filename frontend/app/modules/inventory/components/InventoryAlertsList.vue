<script setup lang="ts">
import type { DataTableColumn } from '~/components/ui/organisms/DataTable.vue'
import InventoryAlertTypeBadge from './InventoryAlertTypeBadge.vue'
import type { InventoryAlert, InventoryAlertType } from '../types/alert'

const props = defineProps<{
  alerts: InventoryAlert[]
}>()

const columns: DataTableColumn[] = [
  { key: 'product', label: 'Produto' },
  { key: 'type', label: 'Criticidade' },
  { key: 'currentStock', label: 'Saldo', align: 'right' },
  { key: 'minimumStock', label: 'Minimo', align: 'right' },
  { key: 'shortageQuantity', label: 'Reposicao', align: 'right' },
  { key: 'action', label: '', align: 'right' },
]

const rows = computed(() => {
  return props.alerts.map((alert) => ({
    product: alert.product.name,
    sku: alert.product.sku,
    type: alert.type,
    currentStock: alert.currentStock,
    minimumStock: alert.minimumStock,
    shortageQuantity: alert.shortageQuantity,
    action: alert.productId,
  }))
})
</script>

<template>
  <DataTable
    :columns="columns"
    :rows="rows"
    empty-title="Nenhum alerta encontrado"
    empty-description="Quando uma peca ficar abaixo do minimo ou zerar, ela aparece aqui."
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

    <template #cell-type="{ value }">
      <InventoryAlertTypeBadge :type="value as InventoryAlertType" />
    </template>

    <template #cell-currentStock="{ value }">
      <span
        class="font-medium"
        :class="Number(value) === 0 ? 'text-destructive' : 'text-amber-700'"
      >
        {{ value }}
      </span>
    </template>

    <template #cell-shortageQuantity="{ value }">
      <span class="font-medium">
        {{ value }}
      </span>
    </template>

    <template #cell-action>
      <NuxtLink
        to="/stock"
        class="font-medium text-primary hover:underline"
      >
        Ver estoque
      </NuxtLink>
    </template>
  </DataTable>
</template>

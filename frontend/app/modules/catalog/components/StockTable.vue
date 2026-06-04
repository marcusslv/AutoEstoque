<script setup lang="ts">
import type { DataTableColumn } from '~/components/ui/organisms/DataTable.vue'
import { formatMoney } from '~/shared/utils/format'
import StockStatusBadge from './StockStatusBadge.vue'
import type { StockItem } from '../types/stock'

const props = defineProps<{
  items: StockItem[]
}>()

const columns: DataTableColumn[] = [
  { key: 'product', label: 'Produto' },
  { key: 'sku', label: 'SKU' },
  { key: 'brand', label: 'Marca' },
  { key: 'currentStock', label: 'Saldo', align: 'right' },
  { key: 'minimumStock', label: 'Minimo', align: 'right' },
  { key: 'stockStatus', label: 'Status' },
  { key: 'cost', label: 'Custo', align: 'right' },
]

const rows = computed(() => {
  return props.items.map((item) => ({
    product: item.name,
    sku: item.sku,
    brand: item.brand ?? '-',
    category: item.category ?? '-',
    currentStock: item.currentStock,
    minimumStock: item.minimumStock,
    stockStatus: item.stockStatus,
    cost: formatMoney(item.costInCents, item.currency),
  }))
})
</script>

<template>
  <DataTable
    :columns="columns"
    :rows="rows"
    empty-title="Nenhum produto encontrado"
    empty-description="Ajuste a busca ou cadastre produtos para consultar o estoque."
  >
    <template #cell-product="{ row, value }">
      <div class="min-w-0">
        <p class="truncate font-medium">
          {{ value }}
        </p>
        <p class="truncate text-xs text-muted-foreground">
          {{ row.category }}
        </p>
      </div>
    </template>

    <template #cell-currentStock="{ value }">
      <span
        class="font-medium"
        :class="Number(value) === 0 ? 'text-destructive' : 'text-foreground'"
      >
        {{ value }}
      </span>
    </template>

    <template #cell-stockStatus="{ value }">
      <StockStatusBadge :status="value as StockItem['stockStatus']" />
    </template>
  </DataTable>
</template>

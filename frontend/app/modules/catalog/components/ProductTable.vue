<script setup lang="ts">
import type { DataTableColumn } from '~/components/ui/organisms/DataTable.vue'
import type { StockItem } from '../types/stock'

const props = defineProps<{
  products: StockItem[]
}>()

defineEmits<{
  edit: [product: StockItem]
}>()

const columns: DataTableColumn[] = [
  { key: 'product', label: 'Produto' },
  { key: 'sku', label: 'SKU' },
  { key: 'category', label: 'Categoria' },
  { key: 'brand', label: 'Marca' },
  { key: 'minimumStock', label: 'Minimo', align: 'right' },
  { key: 'cost', label: 'Custo', align: 'right' },
  { key: 'actions', label: '', align: 'right' },
]

const formatMoney = (valueInCents: number, currency: string) => {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency,
  }).format(valueInCents / 100)
}

const rows = computed(() => {
  return props.products.map((product) => ({
    id: product.id,
    product: product.name,
    sku: product.sku,
    barcode: product.barcode ?? '-',
    category: product.category ?? '-',
    brand: product.brand ?? '-',
    minimumStock: product.minimumStock,
    cost: formatMoney(product.costInCents, product.currency),
    original: product,
  }))
})
</script>

<template>
  <DataTable
    :columns="columns"
    :rows="rows"
    empty-title="Nenhum produto encontrado"
    empty-description="Cadastre produtos ou ajuste a busca para continuar."
  >
    <template #cell-product="{ row, value }">
      <div class="min-w-0">
        <p class="truncate font-medium">
          {{ value }}
        </p>
        <p class="truncate text-xs text-muted-foreground">
          Codigo: {{ row.barcode }}
        </p>
      </div>
    </template>

    <template #cell-actions="{ row }">
      <AppButton
        size="sm"
        variant="secondary"
        @click="$emit('edit', row.original as StockItem)"
      >
        Editar
      </AppButton>
    </template>
  </DataTable>
</template>

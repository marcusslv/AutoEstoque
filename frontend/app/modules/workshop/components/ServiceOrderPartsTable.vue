<script setup lang="ts">
import type { DataTableColumn } from '~/components/ui/organisms/DataTable.vue'
import type { ServiceOrderPart } from '../types/serviceOrder'

const props = defineProps<{
  parts: ServiceOrderPart[]
}>()

const columns: DataTableColumn[] = [
  { key: 'product', label: 'Peca' },
  { key: 'quantity', label: 'Qtd.', align: 'right' },
  { key: 'movementsTotal', label: 'Baixas', align: 'right' },
]

const rows = computed(() => {
  return props.parts.map((part) => ({
    product: part.productName,
    sku: part.productSku,
    quantity: part.quantity,
    movementsTotal: part.movementsTotal,
    movements: part.movements,
  }))
})
</script>

<template>
  <DataTable
    :columns="columns"
    :rows="rows"
    empty-title="Nenhuma peca adicionada"
    empty-description="Adicione as pecas utilizadas antes de finalizar a OS."
  >
    <template #cell-product="{ row, value }">
      <div class="min-w-0">
        <p class="truncate font-medium">
          {{ value }}
        </p>
        <p class="truncate text-xs text-muted-foreground">
          {{ row.sku }}
        </p>
        <div v-if="Array.isArray(row.movements) && row.movements.length" class="mt-2 space-y-1 text-xs text-muted-foreground">
          <p
            v-for="movement in row.movements"
            :key="movement.id"
          >
            Movimento {{ movement.id }} - {{ movement.quantity }} un.
          </p>
        </div>
      </div>
    </template>
  </DataTable>
</template>

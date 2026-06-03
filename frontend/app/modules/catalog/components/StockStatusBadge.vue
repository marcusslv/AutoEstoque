<script setup lang="ts">
import type { StockStatus } from '../types/stock'

const props = defineProps<{
  status: StockStatus
}>()

const statusMap: Record<StockStatus, { label: string, tone: 'success' | 'warning' | 'danger' }> = {
  available: {
    label: 'Disponivel',
    tone: 'success',
  },
  minimum: {
    label: 'Abaixo do minimo',
    tone: 'warning',
  },
  zero: {
    label: 'Zerado',
    tone: 'danger',
  },
}
</script>

<template>
  <StatusBadge
    :label="statusMap[props.status].label"
    :tone="statusMap[props.status].tone"
  />
</template>

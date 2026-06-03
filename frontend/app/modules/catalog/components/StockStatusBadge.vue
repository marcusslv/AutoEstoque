<script setup lang="ts">
import type { StockStatus } from '../types/stock'

const props = defineProps<{
  status?: StockStatus | string | null
}>()

type BadgeConfig = {
  label: string
  tone: 'success' | 'warning' | 'danger' | 'neutral'
}

const statusMap: Record<StockStatus, BadgeConfig> = {
  available: {
    label: 'Disponivel',
    tone: 'success',
  },
  minimum: {
    label: 'Abaixo do minimo',
    tone: 'warning',
  },
  below_minimum: {
    label: 'Abaixo do minimo',
    tone: 'warning',
  },
  zero: {
    label: 'Zerado',
    tone: 'danger',
  },
}

const statusConfig = computed<BadgeConfig>(() => {
  if (!props.status) {
    return {
      label: 'Nao informado',
      tone: 'neutral',
    }
  }

  return statusMap[props.status as StockStatus] ?? {
    label: 'Nao informado',
    tone: 'neutral',
  }
})
</script>

<template>
  <StatusBadge
    :label="statusConfig.label"
    :tone="statusConfig.tone"
  />
</template>

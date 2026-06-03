<script setup lang="ts">
import type { StockMovementDirection } from '../types/movement'

const props = defineProps<{
  direction: StockMovementDirection
}>()

const badge = computed(() => {
  if (props.direction === 'entry') {
    return {
      label: 'Entrada',
      tone: 'success' as const,
    }
  }

  return {
    label: 'Saida',
    tone: 'warning' as const,
  }
})
</script>

<template>
  <StatusBadge :label="badge.label" :tone="badge.tone" />
</template>

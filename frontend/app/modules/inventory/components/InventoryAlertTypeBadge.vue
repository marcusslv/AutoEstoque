<script setup lang="ts">
import type { InventoryAlertType } from '../types/alert'

const props = defineProps<{
  type: InventoryAlertType
}>()

const badge = computed(() => {
  if (props.type === 'zero_stock') {
    return {
      label: 'Zerado',
      tone: 'danger' as const,
    }
  }

  return {
    label: 'Abaixo do minimo',
    tone: 'warning' as const,
  }
})
</script>

<template>
  <StatusBadge :label="badge.label" :tone="badge.tone" />
</template>

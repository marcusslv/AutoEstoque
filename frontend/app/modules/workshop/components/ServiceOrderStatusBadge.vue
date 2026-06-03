<script setup lang="ts">
import type { ServiceOrderStatus } from '../types/serviceOrder'

const props = defineProps<{
  status: ServiceOrderStatus
}>()

const statusMap: Record<ServiceOrderStatus, { label: string, tone: 'success' | 'warning' }> = {
  open: {
    label: 'Aberta',
    tone: 'warning',
  },
  finished: {
    label: 'Finalizada',
    tone: 'success',
  },
}
</script>

<template>
  <StatusBadge
    :label="statusMap[props.status].label"
    :tone="statusMap[props.status].tone"
  />
</template>

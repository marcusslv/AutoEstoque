<script setup lang="ts">
import type { StockMovementServiceOrder } from '../types/movement'

const props = defineProps<{
  serviceOrder: StockMovementServiceOrder | null
}>()
</script>

<template>
  <NuxtLink
    v-if="props.serviceOrder"
    :to="`/service-orders/${props.serviceOrder.id}`"
    class="font-medium text-primary hover:underline"
  >
    OS vinculada
  </NuxtLink>

  <span v-else class="text-muted-foreground">
    Manual
  </span>
</template>

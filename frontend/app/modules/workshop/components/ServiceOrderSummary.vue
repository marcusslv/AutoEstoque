<script setup lang="ts">
import ServiceOrderStatusBadge from './ServiceOrderStatusBadge.vue'
import type { ServiceOrderDetails } from '../types/serviceOrder'

const props = defineProps<{
  serviceOrder: ServiceOrderDetails
}>()

const formatDate = (value: string | null) => {
  if (!value) return '-'

  return new Intl.DateTimeFormat('pt-BR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  }).format(new Date(value))
}
</script>

<template>
  <section class="space-y-4 rounded-lg border bg-card p-4">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
      <div>
        <h2 class="text-base font-semibold">
          {{ props.serviceOrder.customerName }}
        </h2>
        <p class="mt-1 text-sm text-muted-foreground">
          {{ props.serviceOrder.vehicle.plate }} · {{ props.serviceOrder.vehicle.brand }} {{ props.serviceOrder.vehicle.model }} · {{ props.serviceOrder.vehicle.year }}
        </p>
      </div>
      <ServiceOrderStatusBadge :status="props.serviceOrder.status" />
    </div>

    <div class="grid gap-4 text-sm sm:grid-cols-3">
      <div>
        <p class="text-xs text-muted-foreground">
          Abertura
        </p>
        <p class="font-medium">
          {{ formatDate(props.serviceOrder.openedAt) }}
        </p>
      </div>
      <div>
        <p class="text-xs text-muted-foreground">
          Finalizacao
        </p>
        <p class="font-medium">
          {{ formatDate(props.serviceOrder.finishedAt) }}
        </p>
      </div>
      <div>
        <p class="text-xs text-muted-foreground">
          Pecas
        </p>
        <p class="font-medium">
          {{ props.serviceOrder.partsTotal }}
        </p>
      </div>
    </div>

    <div class="space-y-2 text-sm">
      <p class="font-medium">
        Servicos
      </p>
      <p class="text-muted-foreground">
        {{ props.serviceOrder.servicesDescription }}
      </p>
    </div>

    <div v-if="props.serviceOrder.observations" class="space-y-2 text-sm">
      <p class="font-medium">
        Observacoes
      </p>
      <p class="text-muted-foreground">
        {{ props.serviceOrder.observations }}
      </p>
    </div>
  </section>
</template>

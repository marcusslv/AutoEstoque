<script setup lang="ts">
import ServiceOrderStatusBadge from './ServiceOrderStatusBadge.vue'
import { formatDateTime } from '~/shared/utils/format'
import type { ServiceOrderDetails } from '../types/serviceOrder'

const props = defineProps<{
  serviceOrder: ServiceOrderDetails
}>()

</script>

<template>
  <section class="space-y-4 rounded-lg border bg-card p-4">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
      <div>
        <h2 class="text-base font-semibold">
          {{ props.serviceOrder.customerName }}
        </h2>
        <p class="mt-1 text-sm text-muted-foreground">
          {{ props.serviceOrder.vehicle.plate }} - {{ props.serviceOrder.vehicle.brand }} {{ props.serviceOrder.vehicle.model }} - {{ props.serviceOrder.vehicle.year }}
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
          {{ formatDateTime(props.serviceOrder.openedAt) }}
        </p>
      </div>
      <div>
        <p class="text-xs text-muted-foreground">
          Finalizacao
        </p>
        <p class="font-medium">
          {{ formatDateTime(props.serviceOrder.finishedAt) }}
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

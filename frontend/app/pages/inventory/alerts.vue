<script setup lang="ts">
import { RefreshCw } from 'lucide-vue-next'
import InventoryAlertsList from '~/modules/inventory/components/InventoryAlertsList.vue'
import { useInventoryAlerts } from '~/modules/inventory/composables/useInventoryAlerts'
import type { InventoryAlertFilter } from '~/modules/inventory/types/alert'
import { cn } from '~/shared/utils/cn'

definePageMeta({
  layout: 'authenticated',
  middleware: ['auth', 'role'],
  permission: 'manual_inventory',
  title: 'Alertas',
})

const activeFilter = ref<InventoryAlertFilter>('all')

const {
  alerts,
  total,
  allTotal,
  minimumTotal,
  zeroTotal,
  loading,
  errorMessage,
  isEmpty,
  load,
} = useInventoryAlerts()

const filterItems: {
  label: string
  value: InventoryAlertFilter
  count: Ref<number> | ComputedRef<number>
}[] = [
  { label: 'Todos', value: 'all', count: allTotal },
  { label: 'Zerados', value: 'zero_stock', count: zeroTotal },
  { label: 'Abaixo do minimo', value: 'minimum_stock', count: minimumTotal },
]

const selectFilter = async (filter: InventoryAlertFilter) => {
  activeFilter.value = filter
  await load(activeFilter.value)
}

await load(activeFilter.value)
</script>

<template>
  <ListPageTemplate
    title="Alertas"
    description="Acompanhe pecas zeradas ou abaixo do estoque minimo."
  >
    <template #actions>
      <AppButton
        variant="secondary"
        :loading="loading"
        @click="load(activeFilter)"
      >
        <RefreshCw class="h-4 w-4" />
        Atualizar
      </AppButton>
    </template>

    <template #filters>
      <div class="flex flex-col gap-3 rounded-lg border bg-card p-4 lg:flex-row lg:items-center lg:justify-between">
        <div class="flex flex-wrap gap-2">
          <button
            v-for="item in filterItems"
            :key="item.value"
            type="button"
            :class="cn(
              'inline-flex h-9 items-center gap-2 rounded-md px-3 text-sm font-medium transition-colors',
              activeFilter === item.value
                ? 'bg-primary text-primary-foreground'
                : 'bg-secondary text-secondary-foreground hover:bg-secondary/80',
            )"
            @click="selectFilter(item.value)"
          >
            {{ item.label }}
            <span
              :class="cn(
                'rounded-md px-1.5 py-0.5 text-xs',
                activeFilter === item.value ? 'bg-primary-foreground/20' : 'bg-background text-muted-foreground',
              )"
            >
              {{ item.count.value }}
            </span>
          </button>
        </div>

        <p class="text-sm text-muted-foreground">
          {{ total }} {{ total === 1 ? 'alerta' : 'alertas' }}
        </p>
      </div>
    </template>

    <LoadingState
      v-if="loading && !alerts.length"
      message="Carregando alertas..."
    />

    <ErrorState
      v-else-if="errorMessage"
      title="Erro ao carregar alertas"
      :message="errorMessage"
      retry-label="Tentar novamente"
      @retry="load(activeFilter)"
    />

    <EmptyState
      v-else-if="isEmpty"
      title="Nenhum alerta de estoque"
      description="As pecas estao dentro dos limites definidos para reposicao."
    />

    <InventoryAlertsList
      v-else
      :alerts="alerts"
    />
  </ListPageTemplate>
</template>

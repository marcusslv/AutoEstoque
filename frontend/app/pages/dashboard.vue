<script setup lang="ts">
import { RefreshCw } from 'lucide-vue-next'
import DashboardMetrics from '~/modules/dashboard/components/DashboardMetrics.vue'
import MostConsumedProducts from '~/modules/dashboard/components/MostConsumedProducts.vue'
import RecentMovements from '~/modules/dashboard/components/RecentMovements.vue'
import { useDashboard } from '~/modules/dashboard/composables/useDashboard'

definePageMeta({
  layout: 'authenticated',
  middleware: ['auth', 'role'],
  permission: 'dashboard',
  title: 'Dashboard',
})

const {
  dashboard,
  mostConsumedProducts,
  loading,
  errorMessage,
  isEmpty,
  load,
} = useDashboard({
  recentMovementsLimit: 8,
  mostConsumedLimit: 5,
})

await load()
</script>

<template>
  <DashboardPageTemplate
    title="Dashboard"
    description="Visao operacional da oficina para acompanhar estoque e movimentacoes."
  >
    <template #actions>
      <PermissionGate permission="dashboard">
        <AppButton :loading="loading" @click="load">
          <RefreshCw class="h-4 w-4" />
          Atualizar indicadores
        </AppButton>
      </PermissionGate>
    </template>

    <template v-if="dashboard" #metrics>
      <DashboardMetrics :dashboard="dashboard" />
    </template>

    <LoadingState
      v-if="loading && !dashboard"
      message="Carregando indicadores do dashboard..."
    />

    <ErrorState
      v-else-if="errorMessage"
      title="Erro ao carregar dashboard"
      :message="errorMessage"
      retry-label="Tentar novamente"
      @retry="load"
    />

    <EmptyState
      v-else-if="isEmpty"
      title="Dashboard sem dados"
      description="Cadastre produtos e registre movimentacoes para acompanhar os indicadores da oficina."
    />

    <RecentMovements
      v-else-if="dashboard"
      :movements="dashboard.recentMovements"
    />

    <template v-if="mostConsumedProducts" #aside>
      <MostConsumedProducts :products="mostConsumedProducts.items" />

      <section v-if="dashboard" class="space-y-3 rounded-lg border bg-card p-4">
        <div>
          <h2 class="text-sm font-medium">
            Alertas de estoque
          </h2>
          <p class="mt-1 text-xs text-muted-foreground">
            Situacao atual dos itens monitorados.
          </p>
        </div>

        <div class="space-y-2">
          <StatusBadge
            :label="`${dashboard.productsBelowMinimum} abaixo do minimo`"
            :tone="dashboard.productsBelowMinimum > 0 ? 'warning' : 'success'"
          />
          <StatusBadge
            :label="`${dashboard.productsZeroStock} zerados`"
            :tone="dashboard.productsZeroStock > 0 ? 'danger' : 'success'"
          />
        </div>
      </section>
    </template>
  </DashboardPageTemplate>
</template>

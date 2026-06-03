<script setup lang="ts">
import { Bell, Boxes, DollarSign, Repeat2 } from 'lucide-vue-next'
import type { DashboardSummary } from '../types/dashboard'

const props = defineProps<{
  dashboard: DashboardSummary
}>()

const formatMoney = (valueInCents: number) => {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL',
  }).format(valueInCents / 100)
}
</script>

<template>
  <MetricCard
    label="Produtos"
    :value="props.dashboard.totalProducts"
    description="Itens cadastrados"
  >
    <template #icon>
      <Boxes class="h-5 w-5" />
    </template>
  </MetricCard>

  <MetricCard
    label="Abaixo do minimo"
    :value="props.dashboard.productsBelowMinimum"
    description="Precisam de reposicao"
  >
    <template #icon>
      <Bell class="h-5 w-5" />
    </template>
  </MetricCard>

  <MetricCard
    label="Valor em estoque"
    :value="formatMoney(props.dashboard.totalStockValueInCents)"
    description="Custo estimado atual"
  >
    <template #icon>
      <DollarSign class="h-5 w-5" />
    </template>
  </MetricCard>

  <MetricCard
    label="Movimentacoes hoje"
    :value="props.dashboard.todayMovements"
    description="Entradas e saidas do dia"
  >
    <template #icon>
      <Repeat2 class="h-5 w-5" />
    </template>
  </MetricCard>
</template>

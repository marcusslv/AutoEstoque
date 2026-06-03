<script setup lang="ts">
import { Bell, Boxes, PackageSearch, Wrench } from 'lucide-vue-next'

definePageMeta({
  layout: 'authenticated',
  middleware: 'auth',
  title: 'Dashboard',
})

const columns = [
  { key: 'event', label: 'Movimentacao' },
  { key: 'user', label: 'Usuario' },
  { key: 'time', label: 'Horario', align: 'right' as const },
]

const rows = [
  { event: 'Entrada de filtro de oleo', user: 'Usuario Demo', time: '08:30' },
  { event: 'Saida de pastilha de freio', user: 'Usuario Demo', time: '09:15' },
  { event: 'Ajuste de estoque', user: 'Usuario Demo', time: '10:05' },
]
</script>

<template>
  <DashboardPageTemplate
    title="Dashboard"
    description="Visao operacional da oficina para acompanhar estoque e movimentacoes."
  >
    <template #actions>
      <AppButton>
        Atualizar indicadores
      </AppButton>
    </template>

    <template #metrics>
      <MetricCard label="Produtos" value="248" description="Itens cadastrados">
        <template #icon>
          <Boxes class="h-5 w-5" />
        </template>
      </MetricCard>
      <MetricCard label="Abaixo do minimo" value="14" description="Precisam de reposicao">
        <template #icon>
          <Bell class="h-5 w-5" />
        </template>
      </MetricCard>
      <MetricCard label="Valor em estoque" value="R$ 42.850" description="Estimativa atual">
        <template #icon>
          <PackageSearch class="h-5 w-5" />
        </template>
      </MetricCard>
      <MetricCard label="OS abertas" value="7" description="Servicos em andamento">
        <template #icon>
          <Wrench class="h-5 w-5" />
        </template>
      </MetricCard>
    </template>

    <ListPageTemplate
      title="Movimentacoes recentes"
      description="Eventos operacionais registrados hoje."
    >
      <DataTable :columns="columns" :rows="rows" />
    </ListPageTemplate>

    <template #aside>
      <section class="space-y-3 rounded-lg border bg-card p-4">
        <h2 class="text-sm font-medium">
          Alertas
        </h2>
        <div class="space-y-2">
          <StatusBadge label="Estoque baixo" tone="warning" />
          <StatusBadge label="Estoque zerado" tone="danger" />
          <StatusBadge label="Operacao normal" tone="success" />
        </div>
      </section>
    </template>
  </DashboardPageTemplate>
</template>

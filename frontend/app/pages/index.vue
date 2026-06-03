<script setup lang="ts">
import { Boxes, ClipboardList, PackageSearch, Plus, RefreshCw } from 'lucide-vue-next'

definePageMeta({
  layout: 'authenticated',
  title: 'Atomic Design',
})

const config = useRuntimeConfig()

const search = ref('')
const startDate = ref('')
const endDate = ref('')

const columns = [
  { key: 'name', label: 'Componente' },
  { key: 'layer', label: 'Camada' },
  { key: 'status', label: 'Status' },
]

const rows = computed(() => {
  const items = [
    { name: 'AppButton', layer: 'Atom', status: 'Pronto' },
    { name: 'FormField', layer: 'Molecule', status: 'Pronto' },
    { name: 'DataTable', layer: 'Organism', status: 'Pronto' },
    { name: 'DashboardPageTemplate', layer: 'Template', status: 'Pronto' },
  ]

  if (!search.value) {
    return items
  }

  const term = search.value.toLowerCase()

  return items.filter((item) => {
    return item.name.toLowerCase().includes(term)
      || item.layer.toLowerCase().includes(term)
      || item.status.toLowerCase().includes(term)
  })
})
</script>

<template>
  <DashboardPageTemplate
    title="AutoEstoque Front-end"
    description="Base Atomic Design pronta para iniciar as telas de negocio."
  >
    <template #actions>
      <AppIconButton label="Atualizar">
        <RefreshCw class="h-4 w-4" />
      </AppIconButton>
      <AppButton>
        <Plus class="h-4 w-4" />
        Nova acao
      </AppButton>
    </template>

    <template #metrics>
      <MetricCard label="Atoms" value="9" description="Blocos visuais basicos">
        <template #icon>
          <Boxes class="h-5 w-5" />
        </template>
      </MetricCard>
      <MetricCard label="Molecules" value="6" description="Composicoes pequenas">
        <template #icon>
          <PackageSearch class="h-5 w-5" />
        </template>
      </MetricCard>
      <MetricCard label="Organisms" value="4" description="Estruturas reutilizaveis">
        <template #icon>
          <ClipboardList class="h-5 w-5" />
        </template>
      </MetricCard>
      <MetricCard label="API Base" :value="String(config.public.apiBaseUrl)" description="Configuracao publica do Nuxt" />
    </template>

    <ListPageTemplate
      title="Componentes compartilhados"
      description="Exemplo visual usando a camada Atomic Design sem regras de dominio."
    >
      <template #filters>
        <FilterToolbar
          v-model:search="search"
          search-placeholder="Buscar componente"
          @reset="search = ''"
        >
          <AppBadge>Fase 1</AppBadge>
        </FilterToolbar>
      </template>

      <DataTable
        :columns="columns"
        :rows="rows"
        empty-title="Nenhum componente encontrado"
        empty-description="Ajuste a busca para visualizar os componentes."
      >
        <template #cell-status="{ value }">
          <StatusBadge :label="String(value)" tone="success" />
        </template>
      </DataTable>
    </ListPageTemplate>

    <template #aside>
      <section class="space-y-4 rounded-lg border bg-card p-4">
        <div class="space-y-1">
          <h2 class="text-sm font-medium">
            Formularios
          </h2>
          <p class="text-sm text-muted-foreground">
            Exemplo de campos atomicos e molecules.
          </p>
        </div>

        <FormField label="Nome">
          <AppInput placeholder="Exemplo" />
        </FormField>

        <FormField label="Periodo">
          <DateRangeFilter v-model:start-date="startDate" v-model:end-date="endDate" />
        </FormField>

        <FormField label="Observacao">
          <AppTextarea placeholder="Texto livre" />
        </FormField>

        <AppSeparator />

        <div class="flex items-center justify-between text-sm">
          <span class="text-muted-foreground">Valor exemplo</span>
          <MoneyDisplay :value="1280.5" />
        </div>
      </section>
    </template>
  </DashboardPageTemplate>
</template>

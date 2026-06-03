<script setup lang="ts">
import { RefreshCw } from 'lucide-vue-next'
import StockTable from '~/modules/catalog/components/StockTable.vue'
import { useStock } from '~/modules/catalog/composables/useStock'

definePageMeta({
  layout: 'authenticated',
  middleware: ['auth', 'role'],
  permission: 'inventory',
  title: 'Estoque',
})

const search = ref('')
const {
  items,
  total,
  loading,
  errorMessage,
  isEmpty,
  load,
} = useStock()

let searchTimeout: ReturnType<typeof setTimeout> | null = null

watch(search, (value) => {
  if (searchTimeout) {
    clearTimeout(searchTimeout)
  }

  searchTimeout = setTimeout(() => {
    void load(value)
  }, 350)
})

onBeforeUnmount(() => {
  if (searchTimeout) {
    clearTimeout(searchTimeout)
  }
})

await load()
</script>

<template>
  <ListPageTemplate
    title="Estoque"
    description="Consulte saldos, status e itens que precisam de reposicao."
  >
    <template #actions>
      <AppButton :loading="loading" @click="load(search)">
        <RefreshCw class="h-4 w-4" />
        Atualizar
      </AppButton>
    </template>

    <template #filters>
      <div class="flex flex-col gap-3 rounded-lg border bg-card p-4 sm:flex-row sm:items-center sm:justify-between">
        <SearchInput
          v-model="search"
          class="w-full sm:max-w-sm"
          placeholder="Buscar por produto, SKU, marca ou categoria"
        />

        <p class="text-sm text-muted-foreground">
          {{ total }} produto{{ total === 1 ? '' : 's' }}
        </p>
      </div>
    </template>

    <LoadingState
      v-if="loading && !items.length"
      message="Carregando estoque..."
    />

    <ErrorState
      v-else-if="errorMessage"
      title="Erro ao carregar estoque"
      :message="errorMessage"
      retry-label="Tentar novamente"
      @retry="load(search)"
    />

    <EmptyState
      v-else-if="isEmpty && !search"
      title="Estoque vazio"
      description="Produtos cadastrados aparecem aqui com saldo atual, mesmo quando ainda nao tiveram movimentacao."
    />

    <StockTable
      v-else
      :items="items"
    />
  </ListPageTemplate>
</template>

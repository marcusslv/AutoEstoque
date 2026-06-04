<script setup lang="ts">
import { Plus, RefreshCw } from 'lucide-vue-next'
import ProductDialog from '~/modules/catalog/components/ProductDialog.vue'
import ProductTable from '~/modules/catalog/components/ProductTable.vue'
import { useProducts } from '~/modules/catalog/composables/useProducts'
import type { ProductFormValues } from '~/modules/catalog/types/product'
import type { StockItem } from '~/modules/catalog/types/stock'
import { getApiErrorMessage } from '~/shared/api/apiErrors'
import { useToast } from '~/shared/feedback/useToast'

definePageMeta({
  layout: 'authenticated',
  middleware: ['auth', 'role'],
  permission: 'catalog',
  title: 'Produtos',
})

const search = ref('')
const toast = useToast()
const dialogOpen = ref(false)
const selectedProduct = ref<StockItem | null>(null)
const saveErrorMessage = ref<string | null>(null)
const {
  products,
  total,
  loading,
  saving,
  errorMessage,
  validationErrors,
  isEmpty,
  load,
  create,
  update,
} = useProducts()

let searchTimeout: ReturnType<typeof setTimeout> | null = null

const openCreateDialog = () => {
  selectedProduct.value = null
  saveErrorMessage.value = null
  validationErrors.value = {}
  dialogOpen.value = true
}

const openEditDialog = (product: StockItem) => {
  selectedProduct.value = product
  saveErrorMessage.value = null
  validationErrors.value = {}
  dialogOpen.value = true
}

const closeDialog = () => {
  dialogOpen.value = false
  selectedProduct.value = null
  saveErrorMessage.value = null
}

const saveProduct = async (values: ProductFormValues) => {
  saveErrorMessage.value = null

  try {
    if (selectedProduct.value) {
      await update(selectedProduct.value.id, values)
      toast.success('Produto atualizado')
    } else {
      await create(values)
      toast.success('Produto cadastrado')
    }

    closeDialog()
    await load(search.value)
  } catch (error) {
    saveErrorMessage.value = getApiErrorMessage(error, 'Nao foi possivel salvar o produto.')
  }
}

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
    title="Produtos"
    description="Cadastre e mantenha as pecas controladas pela oficina."
  >
    <template #actions>
      <div class="flex items-center gap-2">
        <AppButton
          variant="secondary"
          :loading="loading"
          @click="load(search)"
        >
          <RefreshCw class="h-4 w-4" />
          Atualizar
        </AppButton>
        <AppButton @click="openCreateDialog">
          <Plus class="h-4 w-4" />
          Novo produto
        </AppButton>
      </div>
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
      v-if="loading && !products.length"
      message="Carregando produtos..."
    />

    <ErrorState
      v-else-if="errorMessage"
      title="Erro ao carregar produtos"
      :message="errorMessage"
      retry-label="Tentar novamente"
      @retry="load(search)"
    />

    <EmptyState
      v-else-if="isEmpty && !search"
      title="Nenhum produto cadastrado"
      description="Cadastre o primeiro produto para iniciar o controle de estoque."
      action-label="Novo produto"
      @action="openCreateDialog"
    />

    <ProductTable
      v-else
      :products="products"
      @edit="openEditDialog"
    />

    <ErrorState
      v-if="saveErrorMessage && dialogOpen"
      class="fixed bottom-4 right-4 z-[60] max-w-sm"
      title="Erro ao salvar produto"
      :message="saveErrorMessage"
    />

    <ProductDialog
      :open="dialogOpen"
      :product="selectedProduct"
      :submitting="saving"
      :errors="validationErrors"
      @close="closeDialog"
      @submit="saveProduct"
    />
  </ListPageTemplate>
</template>

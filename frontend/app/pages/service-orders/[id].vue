<script setup lang="ts">
import { ArrowLeft, CheckCircle, Plus, RefreshCw } from 'lucide-vue-next'
import type { AppSelectOption } from '~/components/ui/atoms/AppSelect.vue'
import AddPartDialog from '~/modules/workshop/components/AddPartDialog.vue'
import ServiceOrderPartsTable from '~/modules/workshop/components/ServiceOrderPartsTable.vue'
import ServiceOrderSummary from '~/modules/workshop/components/ServiceOrderSummary.vue'
import { useServiceOrderDetails } from '~/modules/workshop/composables/useServiceOrderDetails'
import { createCatalogApi } from '~/modules/catalog/services/catalogApi'
import type { AddPartFormValues } from '~/modules/workshop/types/serviceOrder'

definePageMeta({
  layout: 'authenticated',
  middleware: ['auth', 'role'],
  permission: 'workshop',
  title: 'Detalhe da OS',
})

const route = useRoute()
const serviceOrderId = String(route.params.id)
const { $api } = useNuxtApp()
const catalogApi = createCatalogApi($api)
const partDialogOpen = ref(false)
const finishDialogOpen = ref(false)
const productOptions = ref<AppSelectOption[]>([])
const {
  serviceOrder,
  loading,
  saving,
  finishing,
  errorMessage,
  actionErrorMessage,
  validationErrors,
  isFinished,
  load,
  addPart,
  finish,
} = useServiceOrderDetails(serviceOrderId)

const loadProductOptions = async () => {
  const response = await catalogApi.listStock()
  productOptions.value = response.items.map((product) => ({
    label: `${product.name} · ${product.sku} · saldo ${product.currentStock}`,
    value: product.id,
    disabled: product.currentStock <= 0,
  }))
}

const openPartDialog = async () => {
  validationErrors.value = {}
  actionErrorMessage.value = null
  await loadProductOptions()
  partDialogOpen.value = true
}

const closePartDialog = () => {
  partDialogOpen.value = false
}

const savePart = async (values: AddPartFormValues) => {
  try {
    await addPart(values)
    closePartDialog()
  } catch {
    // Error state is exposed by the composable.
  }
}

const confirmFinish = async () => {
  try {
    await finish()
    finishDialogOpen.value = false
  } catch {
    // Error state is exposed by the composable.
  }
}

await load()
</script>

<template>
  <DetailPageTemplate
    title="Detalhe da OS"
    description="Visualize pecas, movimentacoes e finalize o atendimento."
  >
    <template #actions>
      <div class="flex flex-wrap items-center gap-2">
        <NuxtLink to="/service-orders">
          <AppButton variant="secondary">
            <ArrowLeft class="h-4 w-4" />
            Voltar
          </AppButton>
        </NuxtLink>

        <AppButton
          variant="secondary"
          :loading="loading"
          @click="load"
        >
          <RefreshCw class="h-4 w-4" />
          Atualizar
        </AppButton>

        <AppButton
          v-if="serviceOrder && !isFinished"
          @click="openPartDialog"
        >
          <Plus class="h-4 w-4" />
          Adicionar peca
        </AppButton>

        <AppButton
          v-if="serviceOrder && !isFinished"
          variant="danger"
          @click="finishDialogOpen = true"
        >
          <CheckCircle class="h-4 w-4" />
          Finalizar OS
        </AppButton>
      </div>
    </template>

    <LoadingState
      v-if="loading && !serviceOrder"
      message="Carregando ordem de servico..."
    />

    <ErrorState
      v-else-if="errorMessage"
      title="Erro ao carregar OS"
      :message="errorMessage"
      retry-label="Tentar novamente"
      @retry="load"
    />

    <template v-else-if="serviceOrder">
      <ServiceOrderSummary :service-order="serviceOrder" />

      <ErrorState
        v-if="actionErrorMessage"
        title="Erro na operacao"
        :message="actionErrorMessage"
      />

      <ListPageTemplate
        title="Pecas utilizadas"
        description="Pecas vinculadas a esta ordem de servico."
      >
        <ServiceOrderPartsTable :parts="serviceOrder.parts" />
      </ListPageTemplate>
    </template>

    <AddPartDialog
      :open="partDialogOpen"
      :product-options="productOptions"
      :submitting="saving"
      :errors="validationErrors"
      @close="closePartDialog"
      @submit="savePart"
    />

    <ConfirmDialog
      :open="finishDialogOpen"
      title="Finalizar ordem de servico"
      description="A finalizacao vai baixar automaticamente as pecas utilizadas do estoque."
      confirm-label="Finalizar"
      danger
      :loading="finishing"
      @cancel="finishDialogOpen = false"
      @confirm="confirmFinish"
    />
  </DetailPageTemplate>
</template>

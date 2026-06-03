<script setup lang="ts">
import { PackageMinus, PackagePlus, RefreshCw, SlidersHorizontal } from 'lucide-vue-next'
import type { AppSelectOption } from '~/components/ui/atoms/AppSelect.vue'
import { createCatalogApi } from '~/modules/catalog/services/catalogApi'
import MovementHistoryTable from '~/modules/inventory/components/MovementHistoryTable.vue'
import RegisterAdjustmentDialog from '~/modules/inventory/components/RegisterAdjustmentDialog.vue'
import RegisterEntryDialog from '~/modules/inventory/components/RegisterEntryDialog.vue'
import RegisterOutputDialog from '~/modules/inventory/components/RegisterOutputDialog.vue'
import { useMovements } from '~/modules/inventory/composables/useMovements'
import type { StockMovementDirection, StockMovementFilters, StockMovementFormValues, StockMovementType } from '~/modules/inventory/types/movement'
import { getApiErrorMessage } from '~/shared/api/apiErrors'

definePageMeta({
  layout: 'authenticated',
  middleware: ['auth', 'role'],
  permission: 'manual_inventory',
  title: 'Movimentacoes',
})

const { $api } = useNuxtApp()
const catalogApi = createCatalogApi($api)

const productOptions = ref<AppSelectOption[]>([])
const productId = ref('')
const direction = ref<StockMovementDirection | ''>('')
const type = ref<StockMovementType | ''>('')
const occurredFrom = ref('')
const occurredTo = ref('')
const activeDialog = ref<'entry' | 'output' | 'adjustment' | null>(null)
const saveErrorMessage = ref<string | null>(null)
const productOptionsErrorMessage = ref<string | null>(null)

const {
  movements,
  total,
  loading,
  saving,
  errorMessage,
  validationErrors,
  isEmpty,
  load,
  registerEntry,
  registerOutput,
  registerAdjustment,
} = useMovements()

const directionOptions: AppSelectOption[] = [
  { label: 'Todas direcoes', value: '' },
  { label: 'Entrada', value: 'entry' },
  { label: 'Saida', value: 'output' },
]

const typeOptions: AppSelectOption[] = [
  { label: 'Todos os tipos', value: '' },
  { label: 'Compra', value: 'purchase' },
  { label: 'Devolucao', value: 'return' },
  { label: 'Consumo em servico', value: 'service_consumption' },
  { label: 'Perda', value: 'loss' },
  { label: 'Quebra', value: 'breakage' },
  { label: 'Ajuste manual', value: 'manual_adjustment' },
]

const filters = (): StockMovementFilters => ({
  productId: productId.value,
  direction: direction.value,
  type: type.value,
  occurredFrom: occurredFrom.value,
  occurredTo: occurredTo.value,
  limit: 50,
})

const loadProductOptions = async () => {
  productOptionsErrorMessage.value = null

  try {
    const response = await catalogApi.listStock()
    productOptions.value = [
      { label: 'Todas as pecas', value: '' },
      ...response.items.map((item) => ({
        label: `${item.name} - ${item.sku} - saldo ${item.currentStock}`,
        value: item.id,
      })),
    ]
  } catch (error) {
    productOptions.value = []
    productOptionsErrorMessage.value = getApiErrorMessage(error, 'Nao foi possivel carregar as pecas.')
  }
}

const dialogProductOptions = computed(() => productOptions.value.filter((option) => option.value))

const openDialog = async (dialog: 'entry' | 'output' | 'adjustment') => {
  saveErrorMessage.value = null
  validationErrors.value = {}

  if (!dialogProductOptions.value.length) {
    await loadProductOptions()
  }

  activeDialog.value = dialog
}

const closeDialog = () => {
  activeDialog.value = null
  saveErrorMessage.value = null
}

const saveMovement = async (values: StockMovementFormValues) => {
  saveErrorMessage.value = null

  try {
    if (activeDialog.value === 'entry') {
      await registerEntry(values)
    }

    if (activeDialog.value === 'output') {
      await registerOutput(values)
    }

    if (activeDialog.value === 'adjustment') {
      await registerAdjustment(values)
    }

    closeDialog()
    await Promise.all([load(filters()), loadProductOptions()])
  } catch (error) {
    saveErrorMessage.value = getApiErrorMessage(error, 'Nao foi possivel registrar a movimentacao.')
  }
}

const clearFilters = async () => {
  productId.value = ''
  direction.value = ''
  type.value = ''
  occurredFrom.value = ''
  occurredTo.value = ''
  await load(filters())
}

watch([productId, direction, type, occurredFrom, occurredTo], () => {
  void load(filters())
})

await Promise.all([load(filters()), loadProductOptions()])
</script>

<template>
  <ListPageTemplate
    title="Movimentacoes"
    description="Registre entradas, saidas, ajustes e acompanhe o historico do estoque."
  >
    <template #actions>
      <div class="flex flex-wrap items-center gap-2">
        <AppButton
          variant="secondary"
          :loading="loading"
          @click="load(filters())"
        >
          <RefreshCw class="h-4 w-4" />
          Atualizar
        </AppButton>

        <AppButton @click="openDialog('entry')">
          <PackagePlus class="h-4 w-4" />
          Entrada
        </AppButton>

        <AppButton variant="secondary" @click="openDialog('output')">
          <PackageMinus class="h-4 w-4" />
          Saida
        </AppButton>

        <AppButton variant="secondary" @click="openDialog('adjustment')">
          <SlidersHorizontal class="h-4 w-4" />
          Ajuste
        </AppButton>
      </div>
    </template>

    <template #filters>
      <div class="space-y-3 rounded-lg border bg-card p-4">
        <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-5">
          <AppSelect
            v-model="productId"
            :options="productOptions"
            placeholder="Peca"
          />

          <AppSelect
            :model-value="direction"
            :options="directionOptions"
            @update:model-value="direction = $event as StockMovementDirection | ''"
          />

          <AppSelect
            :model-value="type"
            :options="typeOptions"
            @update:model-value="type = $event as StockMovementType | ''"
          />

          <AppInput
            v-model="occurredFrom"
            type="date"
          />

          <AppInput
            v-model="occurredTo"
            type="date"
          />
        </div>

        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
          <p class="text-sm text-muted-foreground">
            {{ total }} {{ total === 1 ? 'movimentacao' : 'movimentacoes' }}
          </p>

          <AppButton variant="ghost" @click="clearFilters">
            Limpar filtros
          </AppButton>
        </div>
      </div>
    </template>

    <LoadingState
      v-if="loading && !movements.length"
      message="Carregando movimentacoes..."
    />

    <ErrorState
      v-else-if="errorMessage"
      title="Erro ao carregar movimentacoes"
      :message="errorMessage"
      retry-label="Tentar novamente"
      @retry="load(filters())"
    />

    <ErrorState
      v-else-if="productOptionsErrorMessage"
      title="Erro ao carregar pecas"
      :message="productOptionsErrorMessage"
      retry-label="Tentar novamente"
      @retry="loadProductOptions"
    />

    <EmptyState
      v-else-if="isEmpty"
      title="Nenhuma movimentacao encontrada"
      description="Registre uma entrada, saida ou ajuste para iniciar o historico."
      action-label="Registrar entrada"
      @action="openDialog('entry')"
    />

    <MovementHistoryTable
      v-else
      :movements="movements"
    />

    <ErrorState
      v-if="saveErrorMessage && activeDialog"
      class="fixed bottom-4 right-4 z-[60] max-w-sm"
      title="Erro ao registrar movimentacao"
      :message="saveErrorMessage"
    />

    <RegisterEntryDialog
      :open="activeDialog === 'entry'"
      :product-options="dialogProductOptions"
      :submitting="saving"
      :errors="validationErrors"
      @close="closeDialog"
      @submit="saveMovement"
    />

    <RegisterOutputDialog
      :open="activeDialog === 'output'"
      :product-options="dialogProductOptions"
      :submitting="saving"
      :errors="validationErrors"
      @close="closeDialog"
      @submit="saveMovement"
    />

    <RegisterAdjustmentDialog
      :open="activeDialog === 'adjustment'"
      :product-options="dialogProductOptions"
      :submitting="saving"
      :errors="validationErrors"
      @close="closeDialog"
      @submit="saveMovement"
    />
  </ListPageTemplate>
</template>

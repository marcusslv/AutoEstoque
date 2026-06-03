<script setup lang="ts">
import { Plus, RefreshCw } from 'lucide-vue-next'
import type { AppSelectOption } from '~/components/ui/atoms/AppSelect.vue'
import ServiceOrderDialog from '~/modules/workshop/components/ServiceOrderDialog.vue'
import ServiceOrderTable from '~/modules/workshop/components/ServiceOrderTable.vue'
import { useServiceOrders } from '~/modules/workshop/composables/useServiceOrders'
import { createWorkshopApi } from '~/modules/workshop/services/workshopApi'
import type { ServiceOrderFormValues, ServiceOrderStatus } from '~/modules/workshop/types/serviceOrder'
import { getApiErrorMessage } from '~/shared/api/apiErrors'

definePageMeta({
  layout: 'authenticated',
  middleware: ['auth', 'role'],
  permission: 'workshop',
  title: 'Ordens de servico',
})

const { $api } = useNuxtApp()
const workshopApi = createWorkshopApi($api)
const search = ref('')
const status = ref<ServiceOrderStatus | ''>('open')
const dialogOpen = ref(false)
const vehicleOptions = ref<AppSelectOption[]>([])
const saveErrorMessage = ref<string | null>(null)
const {
  serviceOrders,
  total,
  loading,
  saving,
  errorMessage,
  validationErrors,
  isEmpty,
  load,
  create,
} = useServiceOrders()

const statusOptions: AppSelectOption[] = [
  { label: 'Abertas', value: 'open' },
  { label: 'Finalizadas', value: 'finished' },
  { label: 'Todas', value: '' },
]

let searchTimeout: ReturnType<typeof setTimeout> | null = null

const currentFilters = () => ({
  search: search.value,
  status: status.value,
})

const loadVehicleOptions = async () => {
  const response = await workshopApi.listVehicles({ limit: 100 })
  vehicleOptions.value = response.items.map((vehicle) => ({
    label: `${vehicle.plate} · ${vehicle.brand} ${vehicle.model} · ${vehicle.ownerName}`,
    value: vehicle.id,
  }))
}

const openDialog = async () => {
  saveErrorMessage.value = null
  validationErrors.value = {}
  await loadVehicleOptions()
  dialogOpen.value = true
}

const closeDialog = () => {
  dialogOpen.value = false
  saveErrorMessage.value = null
}

const saveServiceOrder = async (values: ServiceOrderFormValues) => {
  saveErrorMessage.value = null

  try {
    await create(values)
    closeDialog()
    await load(currentFilters())
  } catch (error) {
    saveErrorMessage.value = getApiErrorMessage(error, 'Nao foi possivel criar a ordem de servico.')
  }
}

watch([search, status], () => {
  if (searchTimeout) {
    clearTimeout(searchTimeout)
  }

  searchTimeout = setTimeout(() => {
    void load(currentFilters())
  }, 350)
})

onBeforeUnmount(() => {
  if (searchTimeout) {
    clearTimeout(searchTimeout)
  }
})

await load(currentFilters())
</script>

<template>
  <ListPageTemplate
    title="Ordens de servico"
    description="Acompanhe servicos em andamento, pecas utilizadas e finalizacoes."
  >
    <template #actions>
      <div class="flex items-center gap-2">
        <AppButton
          variant="secondary"
          :loading="loading"
          @click="load(currentFilters())"
        >
          <RefreshCw class="h-4 w-4" />
          Atualizar
        </AppButton>

        <AppButton @click="openDialog">
          <Plus class="h-4 w-4" />
          Nova OS
        </AppButton>
      </div>
    </template>

    <template #filters>
      <div class="flex flex-col gap-3 rounded-lg border bg-card p-4 lg:flex-row lg:items-center lg:justify-between">
        <div class="flex flex-col gap-3 sm:flex-row">
          <SearchInput
            v-model="search"
            class="w-full sm:w-80"
            placeholder="Buscar por cliente, placa ou servico"
          />
          <AppSelect
            v-model="status"
            class="sm:w-44"
            :options="statusOptions"
          />
        </div>

        <p class="text-sm text-muted-foreground">
          {{ total }} {{ total === 1 ? 'ordem' : 'ordens' }}
        </p>
      </div>
    </template>

    <LoadingState
      v-if="loading && !serviceOrders.length"
      message="Carregando ordens de servico..."
    />

    <ErrorState
      v-else-if="errorMessage"
      title="Erro ao carregar ordens"
      :message="errorMessage"
      retry-label="Tentar novamente"
      @retry="load(currentFilters())"
    />

    <EmptyState
      v-else-if="isEmpty && !search"
      title="Nenhuma ordem encontrada"
      description="Crie uma ordem de servico para iniciar o atendimento."
      action-label="Nova OS"
      @action="openDialog"
    />

    <ServiceOrderTable
      v-else
      :service-orders="serviceOrders"
    />

    <ErrorState
      v-if="saveErrorMessage && dialogOpen"
      class="fixed bottom-4 right-4 z-[60] max-w-sm"
      title="Erro ao criar OS"
      :message="saveErrorMessage"
    />

    <ServiceOrderDialog
      :open="dialogOpen"
      :vehicle-options="vehicleOptions"
      :submitting="saving"
      :errors="validationErrors"
      @close="closeDialog"
      @submit="saveServiceOrder"
    />
  </ListPageTemplate>
</template>

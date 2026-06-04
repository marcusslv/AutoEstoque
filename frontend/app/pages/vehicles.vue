<script setup lang="ts">
import { Plus, RefreshCw } from 'lucide-vue-next'
import VehicleDialog from '~/modules/workshop/components/VehicleDialog.vue'
import VehicleTable from '~/modules/workshop/components/VehicleTable.vue'
import { useVehicles } from '~/modules/workshop/composables/useVehicles'
import type { VehicleFormValues } from '~/modules/workshop/types/vehicle'
import { getApiErrorMessage } from '~/shared/api/apiErrors'
import { useToast } from '~/shared/feedback/useToast'

definePageMeta({
  layout: 'authenticated',
  middleware: ['auth', 'role'],
  permission: 'workshop',
  title: 'Veiculos',
})

const search = ref('')
const toast = useToast()
const dialogOpen = ref(false)
const saveErrorMessage = ref<string | null>(null)
const {
  vehicles,
  total,
  loading,
  saving,
  errorMessage,
  validationErrors,
  isEmpty,
  load,
  create,
} = useVehicles()

let searchTimeout: ReturnType<typeof setTimeout> | null = null

const openDialog = () => {
  saveErrorMessage.value = null
  validationErrors.value = {}
  dialogOpen.value = true
}

const closeDialog = () => {
  dialogOpen.value = false
  saveErrorMessage.value = null
}

const saveVehicle = async (values: VehicleFormValues) => {
  saveErrorMessage.value = null

  try {
    await create(values)
    toast.success('Veiculo cadastrado')
    closeDialog()
    await load(search.value)
  } catch (error) {
    saveErrorMessage.value = getApiErrorMessage(error, 'Nao foi possivel salvar o veiculo.')
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
    title="Veiculos"
    description="Consulte e cadastre veiculos atendidos pela oficina."
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

        <AppButton @click="openDialog">
          <Plus class="h-4 w-4" />
          Novo veiculo
        </AppButton>
      </div>
    </template>

    <template #filters>
      <div class="flex flex-col gap-3 rounded-lg border bg-card p-4 sm:flex-row sm:items-center sm:justify-between">
        <SearchInput
          v-model="search"
          class="w-full sm:max-w-sm"
          placeholder="Buscar por placa, modelo ou proprietario"
        />

        <p class="text-sm text-muted-foreground">
          {{ total }} veiculo{{ total === 1 ? '' : 's' }}
        </p>
      </div>
    </template>

    <LoadingState
      v-if="loading && !vehicles.length"
      message="Carregando veiculos..."
    />

    <ErrorState
      v-else-if="errorMessage"
      title="Erro ao carregar veiculos"
      :message="errorMessage"
      retry-label="Tentar novamente"
      @retry="load(search)"
    />

    <EmptyState
      v-else-if="isEmpty && !search"
      title="Nenhum veiculo cadastrado"
      description="Cadastre veiculos para criar ordens de servico."
      action-label="Novo veiculo"
      @action="openDialog"
    />

    <VehicleTable
      v-else
      :vehicles="vehicles"
    />

    <ErrorState
      v-if="saveErrorMessage && dialogOpen"
      class="fixed bottom-4 right-4 z-[60] max-w-sm"
      title="Erro ao salvar veiculo"
      :message="saveErrorMessage"
    />

    <VehicleDialog
      :open="dialogOpen"
      :submitting="saving"
      :errors="validationErrors"
      @close="closeDialog"
      @submit="saveVehicle"
    />
  </ListPageTemplate>
</template>

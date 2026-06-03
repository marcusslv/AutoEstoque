import { getApiErrorMessage, getApiValidationErrors } from '~/shared/api/apiErrors'
import type { ApiValidationErrors } from '~/shared/api/apiTypes'
import { createInventoryApi } from '../services/inventoryApi'
import type { StockMovement, StockMovementFilters, StockMovementFormValues } from '../types/movement'

export const useMovements = () => {
  const { $api } = useNuxtApp()
  const inventoryApi = createInventoryApi($api)

  const movements = ref<StockMovement[]>([])
  const total = ref(0)
  const loading = ref(false)
  const saving = ref(false)
  const errorMessage = ref<string | null>(null)
  const validationErrors = ref<ApiValidationErrors>({})

  const load = async (filters: StockMovementFilters = {}) => {
    loading.value = true
    errorMessage.value = null

    try {
      const response = await inventoryApi.listMovements(filters)
      movements.value = response.items
      total.value = response.total
    } catch (error) {
      errorMessage.value = getApiErrorMessage(error, 'Nao foi possivel carregar as movimentacoes.')
    } finally {
      loading.value = false
    }
  }

  const withSaving = async (callback: () => Promise<unknown>) => {
    saving.value = true
    validationErrors.value = {}

    try {
      await callback()
    } catch (error) {
      validationErrors.value = getApiValidationErrors(error)
      throw error
    } finally {
      saving.value = false
    }
  }

  const registerEntry = (values: StockMovementFormValues) => {
    return withSaving(() => inventoryApi.registerEntry(values))
  }

  const registerOutput = (values: StockMovementFormValues) => {
    return withSaving(() => inventoryApi.registerOutput(values))
  }

  const registerAdjustment = (values: StockMovementFormValues) => {
    return withSaving(() => inventoryApi.registerAdjustment(values))
  }

  const isEmpty = computed(() => !loading.value && !errorMessage.value && movements.value.length === 0)

  return {
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
  }
}

import { getApiErrorMessage, getApiValidationErrors, isApiError } from '~/shared/api/apiErrors'
import type { ApiValidationErrors } from '~/shared/api/apiTypes'
import { createWorkshopApi } from '../services/workshopApi'
import type { Vehicle, VehicleFormValues } from '../types/vehicle'

export const useVehicles = () => {
  const { $api } = useNuxtApp()
  const workshopApi = createWorkshopApi($api)

  const vehicles = ref<Vehicle[]>([])
  const total = ref(0)
  const loading = ref(false)
  const saving = ref(false)
  const errorMessage = ref<string | null>(null)
  const validationErrors = ref<ApiValidationErrors>({})

  const load = async (search = '') => {
    loading.value = true
    errorMessage.value = null

    try {
      const response = await workshopApi.listVehicles({ search, limit: 50 })
      vehicles.value = response.items
      total.value = response.total
    } catch (error) {
      errorMessage.value = getApiErrorMessage(error, 'Nao foi possivel carregar os veiculos.')
    } finally {
      loading.value = false
    }
  }

  const create = async (values: VehicleFormValues) => {
    saving.value = true
    validationErrors.value = {}

    try {
      await workshopApi.createVehicle(values)
    } catch (error) {
      validationErrors.value = getApiValidationErrors(error)

      if (isApiError(error) && error.kind === 'conflict') {
        validationErrors.value = {
          ...validationErrors.value,
          plate: [error.message],
        }
      }

      throw error
    } finally {
      saving.value = false
    }
  }

  const isEmpty = computed(() => !loading.value && !errorMessage.value && vehicles.value.length === 0)

  return {
    vehicles,
    total,
    loading,
    saving,
    errorMessage,
    validationErrors,
    isEmpty,
    load,
    create,
  }
}

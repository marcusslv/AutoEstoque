import { getApiErrorMessage, getApiValidationErrors } from '~/shared/api/apiErrors'
import type { ApiValidationErrors } from '~/shared/api/apiTypes'
import { createWorkshopApi } from '../services/workshopApi'
import type { ServiceOrderFormValues, ServiceOrderListItem, ServiceOrderStatus } from '../types/serviceOrder'

export const useServiceOrders = () => {
  const { $api } = useNuxtApp()
  const workshopApi = createWorkshopApi($api)

  const serviceOrders = ref<ServiceOrderListItem[]>([])
  const total = ref(0)
  const loading = ref(false)
  const saving = ref(false)
  const errorMessage = ref<string | null>(null)
  const validationErrors = ref<ApiValidationErrors>({})

  const load = async (filters: { search?: string, status?: ServiceOrderStatus | '' } = {}) => {
    loading.value = true
    errorMessage.value = null

    try {
      const response = await workshopApi.listServiceOrders({
        search: filters.search,
        status: filters.status,
        limit: 50,
      })
      serviceOrders.value = response.items
      total.value = response.total
    } catch (error) {
      errorMessage.value = getApiErrorMessage(error, 'Nao foi possivel carregar as ordens de servico.')
    } finally {
      loading.value = false
    }
  }

  const create = async (values: ServiceOrderFormValues) => {
    saving.value = true
    validationErrors.value = {}

    try {
      await workshopApi.createServiceOrder(values)
    } catch (error) {
      validationErrors.value = getApiValidationErrors(error)
      throw error
    } finally {
      saving.value = false
    }
  }

  const isEmpty = computed(() => !loading.value && !errorMessage.value && serviceOrders.value.length === 0)

  return {
    serviceOrders,
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

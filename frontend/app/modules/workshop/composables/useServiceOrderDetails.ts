import { getApiErrorMessage, getApiValidationErrors, isApiError } from '~/shared/api/apiErrors'
import type { ApiValidationErrors } from '~/shared/api/apiTypes'
import { createWorkshopApi } from '../services/workshopApi'
import type { AddPartFormValues, ServiceOrderDetails } from '../types/serviceOrder'

export const useServiceOrderDetails = (serviceOrderId: string) => {
  const { $api } = useNuxtApp()
  const workshopApi = createWorkshopApi($api)

  const serviceOrder = ref<ServiceOrderDetails | null>(null)
  const loading = ref(false)
  const saving = ref(false)
  const finishing = ref(false)
  const errorMessage = ref<string | null>(null)
  const actionErrorMessage = ref<string | null>(null)
  const validationErrors = ref<ApiValidationErrors>({})

  const load = async () => {
    loading.value = true
    errorMessage.value = null

    try {
      serviceOrder.value = await workshopApi.getServiceOrderDetails(serviceOrderId)
    } catch (error) {
      errorMessage.value = getApiErrorMessage(error, 'Nao foi possivel carregar a ordem de servico.')
    } finally {
      loading.value = false
    }
  }

  const addPart = async (values: AddPartFormValues) => {
    saving.value = true
    actionErrorMessage.value = null
    validationErrors.value = {}

    try {
      await workshopApi.addPartToServiceOrder(serviceOrderId, values)
      await load()
    } catch (error) {
      validationErrors.value = getApiValidationErrors(error)
      actionErrorMessage.value = getApiErrorMessage(error, 'Nao foi possivel adicionar a peca.')

      if (isApiError(error) && error.kind === 'conflict') {
        validationErrors.value = {
          ...validationErrors.value,
          quantity: [error.message],
        }
      }

      throw error
    } finally {
      saving.value = false
    }
  }

  const finish = async () => {
    finishing.value = true
    actionErrorMessage.value = null

    try {
      await workshopApi.finishServiceOrder(serviceOrderId)
      await load()
    } catch (error) {
      actionErrorMessage.value = getApiErrorMessage(error, 'Nao foi possivel finalizar a ordem de servico.')
      throw error
    } finally {
      finishing.value = false
    }
  }

  const isFinished = computed(() => serviceOrder.value?.status === 'finished')

  return {
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
  }
}

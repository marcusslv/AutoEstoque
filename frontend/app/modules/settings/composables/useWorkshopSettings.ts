import { getApiErrorMessage, getApiValidationErrors } from '~/shared/api/apiErrors'
import type { ApiValidationErrors } from '~/shared/api/apiTypes'
import { createSettingsApi, toSettingsFormValues } from '../services/settingsApi'
import type { WorkshopSettings, WorkshopSettingsFormValues } from '../types/workshopSettings'

const defaultFormValues = (): WorkshopSettingsFormValues => ({
  displayName: '',
  legalName: '',
  document: '',
  phone: '',
  email: '',
  address: '',
  timezone: 'America/Sao_Paulo',
  currency: 'BRL',
  allowNegativeStock: false,
  autoDeductStockOnServiceOrderFinish: true,
  minimumStockDefault: 0,
  notifyMinimumStock: true,
  notifyZeroStock: true,
  notificationEmail: '',
  notificationPhone: '',
})

export const useWorkshopSettings = () => {
  const { $api } = useNuxtApp()
  const settingsApi = createSettingsApi($api)

  const settings = ref<WorkshopSettings | null>(null)
  const form = ref<WorkshopSettingsFormValues>(defaultFormValues())
  const loading = ref(false)
  const saving = ref(false)
  const errorMessage = ref<string | null>(null)
  const saveErrorMessage = ref<string | null>(null)
  const validationErrors = ref<ApiValidationErrors>({})

  const load = async () => {
    loading.value = true
    errorMessage.value = null

    try {
      const response = await settingsApi.getWorkshopSettings()

      settings.value = response
      form.value = toSettingsFormValues(response)
    } catch (error) {
      errorMessage.value = getApiErrorMessage(error, 'Nao foi possivel carregar as configuracoes.')
    } finally {
      loading.value = false
    }
  }

  const save = async () => {
    saving.value = true
    saveErrorMessage.value = null
    validationErrors.value = {}

    try {
      const response = await settingsApi.updateWorkshopSettings(form.value)

      settings.value = response
      form.value = toSettingsFormValues(response)
    } catch (error) {
      validationErrors.value = getApiValidationErrors(error)
      saveErrorMessage.value = getApiErrorMessage(error, 'Nao foi possivel salvar as configuracoes.')
      throw error
    } finally {
      saving.value = false
    }
  }

  const reset = () => {
    if (!settings.value) {
      form.value = defaultFormValues()

      return
    }

    form.value = toSettingsFormValues(settings.value)
    saveErrorMessage.value = null
    validationErrors.value = {}
  }

  return {
    settings,
    form,
    loading,
    saving,
    errorMessage,
    saveErrorMessage,
    validationErrors,
    load,
    save,
    reset,
  }
}


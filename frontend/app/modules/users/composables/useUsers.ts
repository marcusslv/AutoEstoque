import { getApiErrorMessage, getApiValidationErrors } from '~/shared/api/apiErrors'
import type { ApiValidationErrors } from '~/shared/api/apiTypes'
import { createUsersApi } from '../services/usersApi'
import type { UserFormValues, WorkshopUser } from '../types/user'

export const useUsers = () => {
  const { $api } = useNuxtApp()
  const usersApi = createUsersApi($api)

  const users = ref<WorkshopUser[]>([])
  const total = ref(0)
  const loading = ref(false)
  const saving = ref(false)
  const errorMessage = ref<string | null>(null)
  const validationErrors = ref<ApiValidationErrors>({})

  const load = async () => {
    loading.value = true
    errorMessage.value = null

    try {
      const response = await usersApi.listUsers()
      users.value = response.items
      total.value = response.total
    } catch (error) {
      errorMessage.value = getApiErrorMessage(error, 'Nao foi possivel carregar os usuarios.')
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

  const create = (values: UserFormValues) => {
    return withSaving(() => usersApi.createUser(values))
  }

  const update = (userId: string, values: UserFormValues) => {
    return withSaving(() => usersApi.updateUser(userId, values))
  }

  const deactivate = (userId: string) => {
    return withSaving(() => usersApi.deactivateUser(userId))
  }

  const isEmpty = computed(() => !loading.value && !errorMessage.value && users.value.length === 0)

  return {
    users,
    total,
    loading,
    saving,
    errorMessage,
    validationErrors,
    isEmpty,
    load,
    create,
    update,
    deactivate,
  }
}

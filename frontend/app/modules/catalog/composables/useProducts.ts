import { getApiErrorMessage, getApiValidationErrors } from '~/shared/api/apiErrors'
import type { ApiValidationErrors } from '~/shared/api/apiTypes'
import { createCatalogApi } from '../services/catalogApi'
import type { ProductFormValues } from '../types/product'
import type { StockItem } from '../types/stock'

export const useProducts = () => {
  const { $api } = useNuxtApp()
  const catalogApi = createCatalogApi($api)

  const products = ref<StockItem[]>([])
  const total = ref(0)
  const loading = ref(false)
  const saving = ref(false)
  const errorMessage = ref<string | null>(null)
  const validationErrors = ref<ApiValidationErrors>({})

  const load = async (search = '') => {
    loading.value = true
    errorMessage.value = null

    try {
      const response = await catalogApi.listStock({ search })
      products.value = response.items
      total.value = response.total
    } catch (error) {
      errorMessage.value = getApiErrorMessage(error, 'Nao foi possivel carregar os produtos.')
    } finally {
      loading.value = false
    }
  }

  const create = async (values: ProductFormValues) => {
    saving.value = true
    validationErrors.value = {}

    try {
      await catalogApi.createProduct(values)
    } catch (error) {
      validationErrors.value = getApiValidationErrors(error)
      throw error
    } finally {
      saving.value = false
    }
  }

  const update = async (productId: string, values: ProductFormValues) => {
    saving.value = true
    validationErrors.value = {}

    try {
      await catalogApi.updateProduct(productId, values)
    } catch (error) {
      validationErrors.value = getApiValidationErrors(error)
      throw error
    } finally {
      saving.value = false
    }
  }

  const isEmpty = computed(() => !loading.value && !errorMessage.value && products.value.length === 0)

  return {
    products,
    total,
    loading,
    saving,
    errorMessage,
    validationErrors,
    isEmpty,
    load,
    create,
    update,
  }
}

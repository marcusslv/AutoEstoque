import { getApiErrorMessage } from '~/shared/api/apiErrors'
import { createCatalogApi } from '../services/catalogApi'
import type { StockItem } from '../types/stock'

export const useStock = () => {
  const { $api } = useNuxtApp()
  const catalogApi = createCatalogApi($api)

  const items = ref<StockItem[]>([])
  const total = ref(0)
  const loading = ref(false)
  const errorMessage = ref<string | null>(null)

  const load = async (search = '') => {
    loading.value = true
    errorMessage.value = null

    try {
      const response = await catalogApi.listStock({ search })
      items.value = response.items
      total.value = response.total
    } catch (error) {
      errorMessage.value = getApiErrorMessage(error, 'Nao foi possivel carregar o estoque.')
    } finally {
      loading.value = false
    }
  }

  const isEmpty = computed(() => !loading.value && !errorMessage.value && items.value.length === 0)

  return {
    items,
    total,
    loading,
    errorMessage,
    isEmpty,
    load,
  }
}

import { getApiErrorMessage } from '~/shared/api/apiErrors'
import { createInventoryApi } from '../services/inventoryApi'
import type { InventoryAlert, InventoryAlertFilter } from '../types/alert'

export const useInventoryAlerts = () => {
  const { $api } = useNuxtApp()
  const inventoryApi = createInventoryApi($api)

  const alerts = ref<InventoryAlert[]>([])
  const minimumTotal = ref(0)
  const zeroTotal = ref(0)
  const loading = ref(false)
  const errorMessage = ref<string | null>(null)

  const load = async (filter: InventoryAlertFilter = 'all') => {
    loading.value = true
    errorMessage.value = null

    try {
      const [minimumResponse, zeroResponse] = await Promise.all([
        filter === 'zero_stock'
          ? Promise.resolve({ items: [], total: minimumTotal.value })
          : inventoryApi.listMinimumStockAlerts(),
        filter === 'minimum_stock'
          ? Promise.resolve({ items: [], total: zeroTotal.value })
          : inventoryApi.listZeroStockAlerts(),
      ])

      minimumTotal.value = minimumResponse.total
      zeroTotal.value = zeroResponse.total
      alerts.value = [...zeroResponse.items, ...minimumResponse.items]
    } catch (error) {
      errorMessage.value = getApiErrorMessage(error, 'Nao foi possivel carregar os alertas de estoque.')
    } finally {
      loading.value = false
    }
  }

  const total = computed(() => alerts.value.length)
  const allTotal = computed(() => minimumTotal.value + zeroTotal.value)
  const isEmpty = computed(() => !loading.value && !errorMessage.value && alerts.value.length === 0)

  return {
    alerts,
    total,
    allTotal,
    minimumTotal,
    zeroTotal,
    loading,
    errorMessage,
    isEmpty,
    load,
  }
}

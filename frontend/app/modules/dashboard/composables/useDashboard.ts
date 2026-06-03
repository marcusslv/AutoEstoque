import { getApiErrorMessage } from '~/shared/api/apiErrors'
import { createDashboardApi } from '../services/dashboardApi'
import type { DashboardFilters, DashboardSummary, MostConsumedProductsResult } from '../types/dashboard'

export const useDashboard = (filters: DashboardFilters = {}) => {
  const { $api } = useNuxtApp()
  const dashboardApi = createDashboardApi($api)

  const dashboard = ref<DashboardSummary | null>(null)
  const mostConsumedProducts = ref<MostConsumedProductsResult | null>(null)
  const loading = ref(false)
  const errorMessage = ref<string | null>(null)

  const load = async () => {
    loading.value = true
    errorMessage.value = null

    try {
      const [dashboardResponse, mostConsumedProductsResponse] = await Promise.all([
        dashboardApi.getDashboard({
          recentMovementsLimit: filters.recentMovementsLimit ?? 8,
          date: filters.date,
        }),
        dashboardApi.listMostConsumedProducts({
          mostConsumedLimit: filters.mostConsumedLimit ?? 5,
          periodFrom: filters.periodFrom,
          periodTo: filters.periodTo,
        }),
      ])

      dashboard.value = dashboardResponse
      mostConsumedProducts.value = mostConsumedProductsResponse
    } catch (error) {
      errorMessage.value = getApiErrorMessage(error, 'Nao foi possivel carregar o dashboard.')
    } finally {
      loading.value = false
    }
  }

  const isEmpty = computed(() => {
    return Boolean(
      dashboard.value
      && dashboard.value.totalProducts === 0
      && dashboard.value.todayMovements === 0
      && dashboard.value.recentMovements.length === 0
      && (mostConsumedProducts.value?.items.length ?? 0) === 0,
    )
  })

  return {
    dashboard,
    mostConsumedProducts,
    loading,
    errorMessage,
    isEmpty,
    load,
  }
}

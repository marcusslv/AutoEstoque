import type { ApiClient } from '~/shared/api/apiClient'
import type {
  DashboardFilters,
  DashboardResponse,
  DashboardSummary,
  MostConsumedProductsResponse,
  MostConsumedProductsResult,
} from '../types/dashboard'

const toDashboardQuery = (filters: DashboardFilters) => ({
  date: filters.date,
  recent_movements_limit: filters.recentMovementsLimit,
})

const toMostConsumedQuery = (filters: DashboardFilters) => ({
  period_from: filters.periodFrom,
  period_to: filters.periodTo,
  limit: filters.mostConsumedLimit,
})

const mapDashboard = (response: DashboardResponse): DashboardSummary => ({
  tenantId: response.data.tenant_id,
  date: response.data.date,
  totalProducts: response.data.total_products,
  productsBelowMinimum: response.data.products_below_minimum,
  productsZeroStock: response.data.products_zero_stock,
  totalStockValueInCents: response.data.total_stock_value_in_cents,
  todayMovements: response.data.today_movements,
  recentMovements: response.data.recent_movements.map((movement) => ({
    id: movement.id,
    productId: movement.product_id,
    product: movement.product,
    direction: movement.direction,
    type: movement.type,
    quantity: movement.quantity,
    reason: movement.reason,
    occurredAt: movement.occurred_at,
  })),
})

const mapMostConsumedProducts = (response: MostConsumedProductsResponse): MostConsumedProductsResult => ({
  items: response.data.map((item) => ({
    productId: item.product_id,
    product: item.product,
    totalQuantity: item.total_quantity,
    movementsCount: item.movements_count,
  })),
  meta: {
    tenantId: response.meta.tenant_id ?? '',
    periodFrom: response.meta.period_from ?? null,
    periodTo: response.meta.period_to ?? null,
    total: response.meta.total,
  },
})

export const createDashboardApi = (api: ApiClient) => {
  const getDashboard = async (filters: DashboardFilters = {}) => {
    const response = await api.get<DashboardResponse>('/dashboard', {
      query: toDashboardQuery(filters),
    })

    return mapDashboard(response)
  }

  const listMostConsumedProducts = async (filters: DashboardFilters = {}) => {
    const response = await api.get<MostConsumedProductsResponse>('/dashboard/most-consumed-products', {
      query: toMostConsumedQuery(filters),
    })

    return mapMostConsumedProducts(response)
  }

  return {
    getDashboard,
    listMostConsumedProducts,
  }
}

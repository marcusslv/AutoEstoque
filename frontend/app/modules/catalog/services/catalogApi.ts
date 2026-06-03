import type { ApiClient } from '~/shared/api/apiClient'
import type { StockFilters, StockItem, StockListResponse, StockListResult } from '../types/stock'

const mapStockItem = (item: StockListResponse['data'][number]): StockItem => ({
  id: item.id,
  tenantId: item.tenant_id,
  name: item.name,
  sku: item.sku,
  barcode: item.barcode,
  category: item.category,
  brand: item.brand,
  supplier: item.supplier,
  minimumStock: item.minimum_stock,
  currentStock: item.current_stock,
  stockStatus: item.stock_status,
  costInCents: item.cost_in_cents,
  currency: item.currency,
})

export const createCatalogApi = (api: ApiClient) => {
  const listStock = async (filters: StockFilters = {}): Promise<StockListResult> => {
    const response = await api.get<StockListResponse>('/stock', {
      query: {
        search: filters.search?.trim() || undefined,
      },
    })

    return {
      items: response.data.map(mapStockItem),
      total: response.meta.total,
    }
  }

  return {
    listStock,
  }
}

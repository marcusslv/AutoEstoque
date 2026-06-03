import type { ApiClient } from '~/shared/api/apiClient'
import type { Product, ProductFormValues, ProductPayload, ProductResponse } from '../types/product'
import type { StockFilters, StockItem, StockListResponse, StockListResult } from '../types/stock'

const nullableString = (value: string) => {
  const normalizedValue = value.trim()

  return normalizedValue ? normalizedValue : null
}

const toProductPayload = (values: ProductFormValues): ProductPayload => ({
  name: values.name.trim(),
  sku: values.sku.trim(),
  barcode: nullableString(values.barcode),
  category: nullableString(values.category),
  brand: nullableString(values.brand),
  supplier: nullableString(values.supplier),
  minimum_stock: Number(values.minimumStock) || 0,
  cost_in_cents: Number(values.costInCents) || 0,
  currency: values.currency.trim().toUpperCase() || 'BRL',
})

const mapProduct = (response: ProductResponse): Product => ({
  id: response.data.id,
  tenantId: response.data.tenant_id,
  name: response.data.name,
  sku: response.data.sku,
  barcode: response.data.barcode,
  category: response.data.category,
  brand: response.data.brand,
  supplier: response.data.supplier,
  minimumStock: response.data.minimum_stock,
  costInCents: response.data.cost_in_cents,
  currency: response.data.currency,
})

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

  const createProduct = async (values: ProductFormValues) => {
    const response = await api.post<ProductResponse, ProductPayload>('/products', toProductPayload(values))

    return mapProduct(response)
  }

  const updateProduct = async (productId: string, values: ProductFormValues) => {
    const response = await api.patch<ProductResponse, ProductPayload>(`/products/${productId}`, toProductPayload(values))

    return mapProduct(response)
  }

  return {
    listStock,
    createProduct,
    updateProduct,
  }
}

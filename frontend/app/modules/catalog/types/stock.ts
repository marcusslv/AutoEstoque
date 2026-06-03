export type StockStatus = 'available' | 'minimum' | 'below_minimum' | 'zero'

export type StockItem = {
  id: string
  tenantId: string
  name: string
  sku: string
  barcode: string | null
  category: string | null
  brand: string | null
  supplier: string | null
  minimumStock: number
  currentStock: number
  stockStatus: StockStatus
  costInCents: number
  currency: string
}

export type StockFilters = {
  search?: string
}

export type StockListResult = {
  items: StockItem[]
  total: number
}

export type StockListResponse = {
  data: {
    id: string
    tenant_id: string
    name: string
    sku: string
    barcode: string | null
    category: string | null
    brand: string | null
    supplier: string | null
    minimum_stock: number
    current_stock: number
    stock_status: StockStatus
    cost_in_cents: number
    currency: string
  }[]
  meta: {
    total: number
  }
}

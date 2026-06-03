export type DashboardRecentMovement = {
  id: string
  productId: string
  product: {
    name: string
    sku: string
  }
  direction: 'input' | 'output'
  type: string
  quantity: number
  reason: string
  occurredAt: string
}

export type DashboardSummary = {
  tenantId: string
  date: string
  totalProducts: number
  productsBelowMinimum: number
  productsZeroStock: number
  totalStockValueInCents: number
  todayMovements: number
  recentMovements: DashboardRecentMovement[]
}

export type MostConsumedProduct = {
  productId: string
  product: {
    name: string
    sku: string
  }
  totalQuantity: number
  movementsCount: number
}

export type MostConsumedProductsMeta = {
  tenantId: string
  periodFrom: string | null
  periodTo: string | null
  total: number
}

export type MostConsumedProductsResult = {
  items: MostConsumedProduct[]
  meta: MostConsumedProductsMeta
}

export type DashboardFilters = {
  date?: string
  recentMovementsLimit?: number
  periodFrom?: string
  periodTo?: string
  mostConsumedLimit?: number
}

export type DashboardResponse = {
  data: {
    tenant_id: string
    date: string
    total_products: number
    products_below_minimum: number
    products_zero_stock: number
    total_stock_value_in_cents: number
    today_movements: number
    recent_movements: {
      id: string
      product_id: string
      product: {
        name: string
        sku: string
      }
      direction: 'input' | 'output'
      type: string
      quantity: number
      reason: string
      occurred_at: string
    }[]
  }
}

export type MostConsumedProductsResponse = {
  data: {
    product_id: string
    product: {
      name: string
      sku: string
    }
    total_quantity: number
    movements_count: number
  }[]
  meta: {
    tenant_id?: string
    period_from?: string | null
    period_to?: string | null
    total: number
  }
}

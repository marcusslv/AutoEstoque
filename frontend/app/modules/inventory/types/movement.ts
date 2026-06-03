export type StockMovementDirection = 'entry' | 'output'

export type StockEntryType = 'purchase' | 'manual_adjustment' | 'return'

export type StockOutputType = 'service_consumption' | 'loss' | 'breakage' | 'manual_adjustment'

export type StockMovementType = StockEntryType | StockOutputType

export type StockMovementProduct = {
  name: string
  sku: string
}

export type StockMovementServiceOrder = {
  id: string
  itemId: string
}

export type StockMovement = {
  id: string
  tenantId: string
  productId: string
  product: StockMovementProduct
  userId: string
  direction: StockMovementDirection
  type: StockMovementType
  quantity: number
  reason: string
  note: string | null
  unitCostInCents: number | null
  occurredAt: string
  serviceOrder: StockMovementServiceOrder | null
}

export type StockMovementFilters = {
  productId?: string
  direction?: StockMovementDirection | ''
  type?: StockMovementType | ''
  occurredFrom?: string
  occurredTo?: string
  limit?: number
}

export type StockMovementListResult = {
  items: StockMovement[]
  total: number
}

export type StockMovementResponse = {
  data: {
    id: string
    tenant_id: string
    product_id: string
    product: {
      name: string
      sku: string
    }
    user_id: string
    direction: StockMovementDirection
    type: StockMovementType
    quantity: number
    reason: string
    note: string | null
    unit_cost_in_cents: number | null
    occurred_at: string
    service_order: {
      id: string
      item_id: string
    } | null
  }[]
  meta: {
    total: number
  }
}

export type StockMovementFormValues = {
  productId: string
  type: StockMovementType
  direction: StockMovementDirection
  quantity: number
  reason: string
  note: string
  unitCostInCents: number | null
}

export type RegisterStockEntryPayload = {
  product_id: string
  type: StockEntryType
  quantity: number
  reason: string
  note: string | null
  unit_cost_in_cents: number | null
}

export type RegisterStockOutputPayload = {
  product_id: string
  type: StockOutputType
  quantity: number
  reason: string
  note: string | null
}

export type RegisterStockAdjustmentPayload = {
  product_id: string
  direction: StockMovementDirection
  quantity: number
  reason: string
  note: string | null
}

export type RegisterStockMovementResponse = {
  data: {
    movement_id: string
    inventory_item_id: string
    tenant_id: string
    product_id: string
    direction?: StockMovementDirection
    type: StockMovementType
    quantity: number
    current_stock: number
    reason: string
    note: string | null
    unit_cost_in_cents?: number | null
    occurred_at: string
  }
}

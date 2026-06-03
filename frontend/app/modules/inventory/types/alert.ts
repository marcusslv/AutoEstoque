export type InventoryAlertType = 'minimum_stock' | 'zero_stock'

export type InventoryAlertProduct = {
  name: string
  sku: string
}

export type InventoryAlert = {
  type: InventoryAlertType
  productId: string
  product: InventoryAlertProduct
  currentStock: number
  minimumStock: number
  shortageQuantity: number
}

export type InventoryAlertFilter = InventoryAlertType | 'all'

export type InventoryAlertsResult = {
  items: InventoryAlert[]
  total: number
  minimumTotal: number
  zeroTotal: number
}

export type MinimumStockAlertsResponse = {
  data: {
    type: 'minimum_stock'
    product_id: string
    product: {
      name: string
      sku: string
    }
    current_stock: number
    minimum_stock: number
    shortage_quantity: number
  }[]
  meta: {
    total: number
  }
}

export type ZeroStockAlertsResponse = {
  data: {
    type: 'zero_stock'
    product_id: string
    product: {
      name: string
      sku: string
    }
    current_stock: number
    minimum_stock: number
  }[]
  meta: {
    total: number
  }
}

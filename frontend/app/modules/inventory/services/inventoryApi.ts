import type { ApiClient } from '~/shared/api/apiClient'
import type {
  InventoryAlert,
  MinimumStockAlertsResponse,
  ZeroStockAlertsResponse,
} from '../types/alert'
import type {
  RegisterStockAdjustmentPayload,
  RegisterStockEntryPayload,
  RegisterStockMovementResponse,
  RegisterStockOutputPayload,
  StockEntryType,
  StockMovement,
  StockMovementFilters,
  StockMovementFormValues,
  StockMovementListResult,
  StockMovementResponse,
  StockOutputType,
} from '../types/movement'

const nullableString = (value: string) => {
  const normalizedValue = value.trim()

  return normalizedValue ? normalizedValue : null
}

const mapMovement = (movement: StockMovementResponse['data'][number]): StockMovement => ({
  id: movement.id,
  tenantId: movement.tenant_id,
  productId: movement.product_id,
  product: {
    name: movement.product.name,
    sku: movement.product.sku,
  },
  userId: movement.user_id,
  direction: movement.direction,
  type: movement.type,
  quantity: movement.quantity,
  reason: movement.reason,
  note: movement.note,
  unitCostInCents: movement.unit_cost_in_cents,
  occurredAt: movement.occurred_at,
  serviceOrder: movement.service_order === null
    ? null
    : {
        id: movement.service_order.id,
        itemId: movement.service_order.item_id,
      },
})

const mapMinimumStockAlert = (alert: MinimumStockAlertsResponse['data'][number]): InventoryAlert => ({
  type: 'minimum_stock',
  productId: alert.product_id,
  product: {
    name: alert.product.name,
    sku: alert.product.sku,
  },
  currentStock: alert.current_stock,
  minimumStock: alert.minimum_stock,
  shortageQuantity: alert.shortage_quantity,
})

const mapZeroStockAlert = (alert: ZeroStockAlertsResponse['data'][number]): InventoryAlert => ({
  type: 'zero_stock',
  productId: alert.product_id,
  product: {
    name: alert.product.name,
    sku: alert.product.sku,
  },
  currentStock: alert.current_stock,
  minimumStock: alert.minimum_stock,
  shortageQuantity: Math.max(0, alert.minimum_stock - alert.current_stock),
})

const toEntryPayload = (values: StockMovementFormValues): RegisterStockEntryPayload => ({
  product_id: values.productId,
  type: values.type as StockEntryType,
  quantity: Number(values.quantity) || 1,
  reason: values.reason.trim(),
  note: nullableString(values.note),
  unit_cost_in_cents: values.unitCostInCents === null ? null : Number(values.unitCostInCents) || 0,
})

const toOutputPayload = (values: StockMovementFormValues): RegisterStockOutputPayload => ({
  product_id: values.productId,
  type: values.type as StockOutputType,
  quantity: Number(values.quantity) || 1,
  reason: values.reason.trim(),
  note: nullableString(values.note),
})

const toAdjustmentPayload = (values: StockMovementFormValues): RegisterStockAdjustmentPayload => ({
  product_id: values.productId,
  direction: values.direction,
  quantity: Number(values.quantity) || 1,
  reason: values.reason.trim(),
  note: nullableString(values.note),
})

export const createInventoryApi = (api: ApiClient) => {
  const listMinimumStockAlerts = async (limit = 50) => {
    const response = await api.get<MinimumStockAlertsResponse>('/inventory/alerts/minimum-stock', {
      query: { limit },
    })

    return {
      items: response.data.map(mapMinimumStockAlert),
      total: response.meta.total,
    }
  }

  const listZeroStockAlerts = async (limit = 50) => {
    const response = await api.get<ZeroStockAlertsResponse>('/inventory/alerts/zero-stock', {
      query: { limit },
    })

    return {
      items: response.data.map(mapZeroStockAlert),
      total: response.meta.total,
    }
  }

  const listMovements = async (filters: StockMovementFilters = {}): Promise<StockMovementListResult> => {
    const response = await api.get<StockMovementResponse>('/inventory/movements', {
      query: {
        product_id: filters.productId || undefined,
        direction: filters.direction || undefined,
        type: filters.type || undefined,
        occurred_from: filters.occurredFrom || undefined,
        occurred_to: filters.occurredTo || undefined,
        limit: filters.limit ?? 50,
      },
    })

    return {
      items: response.data.map(mapMovement),
      total: response.meta.total,
    }
  }

  const registerEntry = async (values: StockMovementFormValues) => {
    return api.post<RegisterStockMovementResponse, RegisterStockEntryPayload>('/inventory/entries', toEntryPayload(values))
  }

  const registerOutput = async (values: StockMovementFormValues) => {
    return api.post<RegisterStockMovementResponse, RegisterStockOutputPayload>('/inventory/outputs', toOutputPayload(values))
  }

  const registerAdjustment = async (values: StockMovementFormValues) => {
    return api.post<RegisterStockMovementResponse, RegisterStockAdjustmentPayload>('/inventory/adjustments', toAdjustmentPayload(values))
  }

  return {
    listMinimumStockAlerts,
    listZeroStockAlerts,
    listMovements,
    registerEntry,
    registerOutput,
    registerAdjustment,
  }
}

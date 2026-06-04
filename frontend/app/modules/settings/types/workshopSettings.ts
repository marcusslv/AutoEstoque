export type WorkshopPlan = 'starter' | 'pro'

export type WorkshopSettings = {
  id: string
  tenantId: string
  displayName: string
  legalName: string | null
  document: string | null
  phone: string | null
  email: string | null
  address: string | null
  timezone: string
  currency: string
  allowNegativeStock: boolean
  autoDeductStockOnServiceOrderFinish: boolean
  minimumStockDefault: number
  notifyMinimumStock: boolean
  notifyZeroStock: boolean
  notificationEmail: string | null
  notificationPhone: string | null
  plan: WorkshopPlan
  userLimit: number
  updatedAt: string
}

export type WorkshopSettingsFormValues = {
  displayName: string
  legalName: string
  document: string
  phone: string
  email: string
  address: string
  timezone: string
  currency: string
  allowNegativeStock: boolean
  autoDeductStockOnServiceOrderFinish: boolean
  minimumStockDefault: number
  notifyMinimumStock: boolean
  notifyZeroStock: boolean
  notificationEmail: string
  notificationPhone: string
}

export type WorkshopSettingsResponse = {
  data: {
    id: string
    tenant_id: string
    display_name: string
    legal_name: string | null
    document: string | null
    phone: string | null
    email: string | null
    address: string | null
    timezone: string
    currency: string
    allow_negative_stock: boolean
    auto_deduct_stock_on_service_order_finish: boolean
    minimum_stock_default: number
    notify_minimum_stock: boolean
    notify_zero_stock: boolean
    notification_email: string | null
    notification_phone: string | null
    plan: WorkshopPlan
    user_limit: number
    updated_at: string
  }
}

export type UpdateWorkshopSettingsPayload = {
  display_name: string
  legal_name: string | null
  document: string | null
  phone: string | null
  email: string | null
  address: string | null
  timezone: string
  currency: string
  allow_negative_stock: boolean
  auto_deduct_stock_on_service_order_finish: boolean
  minimum_stock_default: number
  notify_minimum_stock: boolean
  notify_zero_stock: boolean
  notification_email: string | null
  notification_phone: string | null
}


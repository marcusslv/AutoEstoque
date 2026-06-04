import type { ApiClient } from '~/shared/api/apiClient'
import { onlyDigits } from '~/shared/utils/masks'
import type {
  UpdateWorkshopSettingsPayload,
  WorkshopSettings,
  WorkshopSettingsFormValues,
  WorkshopSettingsResponse,
} from '../types/workshopSettings'

const nullableTrim = (value: string) => {
  const trimmed = value.trim()

  return trimmed === '' ? null : trimmed
}

const nullableLowerTrim = (value: string) => {
  const trimmed = nullableTrim(value)

  return trimmed ? trimmed.toLowerCase() : null
}

const nullableDigits = (value: string) => {
  const digits = onlyDigits(value)

  return digits === '' ? null : digits
}

const mapSettings = (settings: WorkshopSettingsResponse['data']): WorkshopSettings => ({
  id: settings.id,
  tenantId: settings.tenant_id,
  displayName: settings.display_name,
  legalName: settings.legal_name,
  document: settings.document,
  phone: settings.phone,
  email: settings.email,
  address: settings.address,
  timezone: settings.timezone,
  currency: settings.currency,
  allowNegativeStock: settings.allow_negative_stock,
  autoDeductStockOnServiceOrderFinish: settings.auto_deduct_stock_on_service_order_finish,
  minimumStockDefault: settings.minimum_stock_default,
  notifyMinimumStock: settings.notify_minimum_stock,
  notifyZeroStock: settings.notify_zero_stock,
  notificationEmail: settings.notification_email,
  notificationPhone: settings.notification_phone,
  plan: settings.plan,
  userLimit: settings.user_limit,
  updatedAt: settings.updated_at,
})

export const toSettingsFormValues = (settings: WorkshopSettings): WorkshopSettingsFormValues => ({
  displayName: settings.displayName,
  legalName: settings.legalName ?? '',
  document: settings.document ?? '',
  phone: settings.phone ?? '',
  email: settings.email ?? '',
  address: settings.address ?? '',
  timezone: settings.timezone,
  currency: settings.currency,
  allowNegativeStock: settings.allowNegativeStock,
  autoDeductStockOnServiceOrderFinish: settings.autoDeductStockOnServiceOrderFinish,
  minimumStockDefault: settings.minimumStockDefault,
  notifyMinimumStock: settings.notifyMinimumStock,
  notifyZeroStock: settings.notifyZeroStock,
  notificationEmail: settings.notificationEmail ?? '',
  notificationPhone: settings.notificationPhone ?? '',
})

export const toUpdateSettingsPayload = (values: WorkshopSettingsFormValues): UpdateWorkshopSettingsPayload => ({
  display_name: values.displayName.trim(),
  legal_name: nullableTrim(values.legalName),
  document: nullableDigits(values.document),
  phone: nullableDigits(values.phone),
  email: nullableLowerTrim(values.email),
  address: nullableTrim(values.address),
  timezone: values.timezone,
  currency: values.currency,
  allow_negative_stock: values.allowNegativeStock,
  auto_deduct_stock_on_service_order_finish: values.autoDeductStockOnServiceOrderFinish,
  minimum_stock_default: Number(values.minimumStockDefault),
  notify_minimum_stock: values.notifyMinimumStock,
  notify_zero_stock: values.notifyZeroStock,
  notification_email: nullableLowerTrim(values.notificationEmail),
  notification_phone: nullableDigits(values.notificationPhone),
})

export const createSettingsApi = (api: ApiClient) => {
  const getWorkshopSettings = async () => {
    const response = await api.get<WorkshopSettingsResponse>('/settings/workshop')

    return mapSettings(response.data)
  }

  const updateWorkshopSettings = async (values: WorkshopSettingsFormValues) => {
    const response = await api.patch<WorkshopSettingsResponse, UpdateWorkshopSettingsPayload>(
      '/settings/workshop',
      toUpdateSettingsPayload(values),
    )

    return mapSettings(response.data)
  }

  return {
    getWorkshopSettings,
    updateWorkshopSettings,
  }
}


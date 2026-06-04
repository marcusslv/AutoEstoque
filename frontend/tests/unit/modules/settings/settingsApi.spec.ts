import { describe, expect, it, vi } from 'vitest'
import type { ApiClient } from '../../../../app/shared/api/apiClient'
import { createSettingsApi } from '../../../../app/modules/settings/services/settingsApi'

const makeApi = () => {
  const api = {
    get: vi.fn(),
    patch: vi.fn(),
  } as unknown as ApiClient & {
    get: ReturnType<typeof vi.fn>
    patch: ReturnType<typeof vi.fn>
  }

  return api
}

describe('settings api', () => {
  it('maps workshop settings response', async () => {
    const api = makeApi()

    api.get.mockResolvedValue({
      data: {
        id: 'settings-1',
        tenant_id: 'tenant-1',
        display_name: 'Oficina Demo',
        legal_name: null,
        document: '12345678000190',
        phone: '11999990000',
        email: 'contato@oficina.test',
        address: null,
        timezone: 'America/Sao_Paulo',
        currency: 'BRL',
        allow_negative_stock: false,
        auto_deduct_stock_on_service_order_finish: true,
        minimum_stock_default: 2,
        notify_minimum_stock: true,
        notify_zero_stock: true,
        notification_email: null,
        notification_phone: null,
        plan: 'starter',
        user_limit: 3,
        updated_at: '2026-06-04T10:00:00.000000Z',
      },
    })

    const result = await createSettingsApi(api).getWorkshopSettings()

    expect(api.get).toHaveBeenCalledWith('/settings/workshop')
    expect(result).toMatchObject({
      id: 'settings-1',
      tenantId: 'tenant-1',
      displayName: 'Oficina Demo',
      minimumStockDefault: 2,
      autoDeductStockOnServiceOrderFinish: true,
      plan: 'starter',
      userLimit: 3,
    })
  })

  it('normalizes update payload', async () => {
    const api = makeApi()

    api.patch.mockResolvedValue({
      data: {
        id: 'settings-1',
        tenant_id: 'tenant-1',
        display_name: 'Oficina Premium',
        legal_name: null,
        document: '12345678000190',
        phone: '11999990000',
        email: 'contato@oficina.test',
        address: null,
        timezone: 'America/Sao_Paulo',
        currency: 'BRL',
        allow_negative_stock: true,
        auto_deduct_stock_on_service_order_finish: false,
        minimum_stock_default: 4,
        notify_minimum_stock: false,
        notify_zero_stock: true,
        notification_email: 'alertas@oficina.test',
        notification_phone: '11988887777',
        plan: 'starter',
        user_limit: 3,
        updated_at: '2026-06-04T10:00:00.000000Z',
      },
    })

    await createSettingsApi(api).updateWorkshopSettings({
      displayName: ' Oficina Premium ',
      legalName: '',
      document: '12.345.678/0001-90',
      phone: '(11) 99999-0000',
      email: 'CONTATO@OFICINA.TEST',
      address: '',
      timezone: 'America/Sao_Paulo',
      currency: 'BRL',
      allowNegativeStock: true,
      autoDeductStockOnServiceOrderFinish: false,
      minimumStockDefault: 4,
      notifyMinimumStock: false,
      notifyZeroStock: true,
      notificationEmail: 'ALERTAS@OFICINA.TEST',
      notificationPhone: '(11) 98888-7777',
    })

    expect(api.patch).toHaveBeenCalledWith('/settings/workshop', {
      display_name: 'Oficina Premium',
      legal_name: null,
      document: '12345678000190',
      phone: '11999990000',
      email: 'contato@oficina.test',
      address: null,
      timezone: 'America/Sao_Paulo',
      currency: 'BRL',
      allow_negative_stock: true,
      auto_deduct_stock_on_service_order_finish: false,
      minimum_stock_default: 4,
      notify_minimum_stock: false,
      notify_zero_stock: true,
      notification_email: 'alertas@oficina.test',
      notification_phone: '11988887777',
    })
  })
})


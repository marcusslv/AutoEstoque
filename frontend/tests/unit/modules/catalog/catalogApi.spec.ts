import { describe, expect, it, vi } from 'vitest'
import { createCatalogApi } from '../../../../app/modules/catalog/services/catalogApi'
import type { ApiClient } from '../../../../app/shared/api/apiClient'

const createApiClientMock = () => ({
  get: vi.fn(),
  post: vi.fn(),
  patch: vi.fn(),
  put: vi.fn(),
  delete: vi.fn(),
  request: vi.fn(),
}) as unknown as ApiClient & {
  get: ReturnType<typeof vi.fn>
  post: ReturnType<typeof vi.fn>
  patch: ReturnType<typeof vi.fn>
}

describe('catalog api', () => {
  it('trims search filters when listing stock', async () => {
    const api = createApiClientMock()
    api.get.mockResolvedValue({
      data: [],
      meta: { total: 0 },
    })

    await createCatalogApi(api).listStock({ search: ' filtro ' })

    expect(api.get).toHaveBeenCalledWith('/stock', {
      query: {
        search: 'filtro',
      },
    })
  })

  it('normalizes product payload before creating', async () => {
    const api = createApiClientMock()
    api.post.mockResolvedValue({
      data: {
        id: 'product-1',
        tenant_id: 'tenant-1',
        name: 'Filtro',
        sku: 'FO-001',
        barcode: null,
        category: null,
        brand: 'Mann',
        supplier: null,
        minimum_stock: 2,
        cost_in_cents: 2590,
        currency: 'BRL',
      },
    })

    const product = await createCatalogApi(api).createProduct({
      name: ' Filtro ',
      sku: ' FO-001 ',
      barcode: ' ',
      category: '',
      brand: ' Mann ',
      supplier: '',
      minimumStock: 2,
      costInCents: 2590,
      currency: 'brl',
    })

    expect(api.post).toHaveBeenCalledWith('/products', {
      name: 'Filtro',
      sku: 'FO-001',
      barcode: null,
      category: null,
      brand: 'Mann',
      supplier: null,
      minimum_stock: 2,
      cost_in_cents: 2590,
      currency: 'BRL',
    })
    expect(product.tenantId).toBe('tenant-1')
  })
})

import { describe, expect, it, vi } from 'vitest'
import { createUsersApi } from '../../../../app/modules/users/services/usersApi'
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

describe('users api', () => {
  it('maps users from API list response', async () => {
    const api = createApiClientMock()
    api.get.mockResolvedValue({
      data: [
        {
          id: 'user-1',
          tenant_id: 'tenant-1',
          name: 'Ana',
          email: 'ana@oficina.test',
          role: 'admin',
          status: 'active',
        },
      ],
      meta: { total: 1 },
    })

    const result = await createUsersApi(api).listUsers()

    expect(result.total).toBe(1)
    expect(result.items[0]).toMatchObject({
      id: 'user-1',
      tenantId: 'tenant-1',
      role: 'admin',
      status: 'active',
    })
  })

  it('normalizes create payload', async () => {
    const api = createApiClientMock()
    api.post.mockResolvedValue({
      data: {
        id: 'user-1',
        tenant_id: 'tenant-1',
        name: 'Ana',
        email: 'ana@oficina.test',
        role: 'manager',
        status: 'active',
      },
    })

    await createUsersApi(api).createUser({
      name: ' Ana ',
      email: ' ANA@OFICINA.TEST ',
      password: 'secret123',
      role: 'manager',
      status: 'active',
    })

    expect(api.post).toHaveBeenCalledWith('/users', {
      name: 'Ana',
      email: 'ana@oficina.test',
      password: 'secret123',
      role: 'manager',
      status: 'active',
    })
  })

  it('uses deactivate endpoint', async () => {
    const api = createApiClientMock()
    api.patch.mockResolvedValue({
      data: {
        id: 'user-1',
        tenant_id: 'tenant-1',
        name: 'Ana',
        email: 'ana@oficina.test',
        role: 'manager',
        status: 'inactive',
      },
    })

    const user = await createUsersApi(api).deactivateUser('user-1')

    expect(api.patch).toHaveBeenCalledWith('/users/user-1/deactivate')
    expect(user.status).toBe('inactive')
  })
})

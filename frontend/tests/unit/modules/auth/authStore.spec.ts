import { createPinia, setActivePinia } from 'pinia'
import { beforeEach, describe, expect, it, vi } from 'vitest'
import { authTokenCookieName, authUserCookieName } from '../../../../app/shared/auth/authToken'
import { setNuxtAppMock } from '../../../setup/vitest.setup'

describe('auth store', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
  })

  it('persists token and authenticated user after login', async () => {
    const post = vi.fn().mockResolvedValue({
      data: {
        access_token: 'token-123',
        token_type: 'Bearer',
        expires_at: null,
        user: {
          id: 'user-1',
          tenant_id: 'tenant-1',
          name: 'Ana',
          email: 'ana@oficina.test',
          role: 'admin',
        },
      },
    })

    setNuxtAppMock({
      $api: {
        post,
      },
    })

    const { useAuthStore } = await import('../../../../app/modules/auth/stores/authStore')
    const store = useAuthStore()
    const user = await store.login({
      email: 'ana@oficina.test',
      password: 'secret123',
    })

    expect(post).toHaveBeenCalledWith('/auth/login', {
      email: 'ana@oficina.test',
      password: 'secret123',
      token_name: 'autoestoque-web',
    })
    expect(store.token).toBe('token-123')
    expect(store.isAuthenticated).toBe(true)
    expect(user.tenantId).toBe('tenant-1')

    expect(useCookie(authTokenCookieName).value).toBe('token-123')
    expect(useCookie(authUserCookieName).value).toMatchObject({
      id: 'user-1',
      tenantId: 'tenant-1',
    })
  })

  it('clears session on logout even when API revocation fails', async () => {
    const post = vi.fn()
      .mockResolvedValueOnce({
        data: {
          access_token: 'token-123',
          token_type: 'Bearer',
          expires_at: null,
          user: {
            id: 'user-1',
            tenant_id: 'tenant-1',
            name: 'Ana',
            email: 'ana@oficina.test',
            role: 'admin',
          },
        },
      })
      .mockRejectedValueOnce(new Error('network'))

    setNuxtAppMock({
      $api: {
        post,
      },
    })

    const { useAuthStore } = await import('../../../../app/modules/auth/stores/authStore')
    const store = useAuthStore()

    await store.login({
      email: 'ana@oficina.test',
      password: 'secret123',
    })

    await expect(store.logout()).rejects.toThrow('network')

    expect(store.token).toBeNull()
    expect(store.user).toBeNull()
    expect(globalThis.navigateTo).toHaveBeenCalledWith('/login')
  })
})

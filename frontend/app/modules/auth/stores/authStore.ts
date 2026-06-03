import { defineStore } from 'pinia'
import { createAuthApi } from '../services/authApi'
import type { AuthenticatedUser, LoginCredentials } from '../types/auth'
import { useAuthSession } from '~/shared/auth/authToken'

const mapAuthenticatedUser = (user: {
  id: string
  tenant_id?: string
  tenantId?: string
  name: string
  email: string
  role: AuthenticatedUser['role']
}): AuthenticatedUser => ({
  id: user.id,
  tenantId: user.tenantId ?? user.tenant_id ?? '',
  name: user.name,
  email: user.email,
  role: user.role,
})

export const useAuthStore = defineStore('auth', () => {
  const { token, user: persistedUser, setToken, setUser, clearSession } = useAuthSession()

  const user = ref<AuthenticatedUser | null>(
    persistedUser.value ? mapAuthenticatedUser(persistedUser.value) : null,
  )
  const loading = ref(false)

  const isAuthenticated = computed(() => Boolean(token.value))

  const login = async (credentials: LoginCredentials) => {
    const { $api } = useNuxtApp()
    const authApi = createAuthApi($api)

    loading.value = true

    try {
      const response = await authApi.login(credentials)
      const authenticatedUser = mapAuthenticatedUser(response.data.user)

      setToken(response.data.access_token)
      setUser(authenticatedUser)
      user.value = authenticatedUser

      return authenticatedUser
    } finally {
      loading.value = false
    }
  }

  const logout = async () => {
    const { $api } = useNuxtApp()
    const authApi = createAuthApi($api)

    loading.value = true

    try {
      if (token.value) {
        await authApi.logout()
      }
    } finally {
      clearSession()
      user.value = null
      loading.value = false
      await navigateTo('/login')
    }
  }

  const clear = () => {
    clearSession()
    user.value = null
  }

  return {
    token,
    user,
    loading,
    isAuthenticated,
    login,
    logout,
    clear,
  }
})

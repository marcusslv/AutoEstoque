import type { Role } from '~/shared/permissions/roles'

export const authTokenCookieName = 'autoestoque_access_token'
export const authUserCookieName = 'autoestoque_authenticated_user'

export type PersistedAuthenticatedUser = {
  id: string
  tenantId: string
  name: string
  email: string
  role: Role
}

export const useAuthToken = () => {
  const token = useCookie<string | null>(authTokenCookieName, {
    sameSite: 'lax',
    watch: true,
  })

  const setToken = (value: string | null) => {
    token.value = value
  }

  const clearToken = () => {
    token.value = null
  }

  return {
    token,
    setToken,
    clearToken,
  }
}

export const useAuthSession = () => {
  const { token, setToken, clearToken } = useAuthToken()
  const user = useCookie<PersistedAuthenticatedUser | null>(authUserCookieName, {
    sameSite: 'lax',
    watch: true,
  })

  const setUser = (value: PersistedAuthenticatedUser | null) => {
    user.value = value
  }

  const clearSession = () => {
    clearToken()
    user.value = null
  }

  return {
    token,
    user,
    setToken,
    setUser,
    clearSession,
  }
}

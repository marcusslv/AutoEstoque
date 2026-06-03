import type { ApiClient } from '~/shared/api/apiClient'
import type { LoginCredentials, LoginResponse, LogoutResponse } from '../types/auth'

export const createAuthApi = (api: ApiClient) => {
  const login = (credentials: LoginCredentials) => {
    return api.post<LoginResponse>('/auth/login', {
      email: credentials.email,
      password: credentials.password,
      token_name: credentials.tokenName ?? 'autoestoque-web',
    })
  }

  const logout = () => {
    return api.post<LogoutResponse>('/auth/logout')
  }

  return {
    login,
    logout,
  }
}

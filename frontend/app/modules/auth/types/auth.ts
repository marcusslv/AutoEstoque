import type { Role } from '~/shared/permissions/roles'

export type AuthenticatedUser = {
  id: string
  tenantId: string
  name: string
  email: string
  role: Role
}

export type LoginCredentials = {
  email: string
  password: string
  tokenName?: string | null
}

export type LoginResponse = {
  data: {
    access_token: string
    token_type: 'Bearer' | string
    expires_at: string | null
    user: {
      id: string
      tenant_id: string
      name: string
      email: string
      role: Role
    }
  }
}

export type LogoutResponse = {
  data: {
    revoked: boolean
  }
}

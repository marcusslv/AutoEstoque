import type { Role } from '~/shared/permissions/roles'

export type UserStatus = 'active' | 'inactive'

export type WorkshopUser = {
  id: string
  tenantId: string
  name: string
  email: string
  role: Role
  status: UserStatus
}

export type UserFormValues = {
  name: string
  email: string
  password: string
  role: Role
  status: UserStatus
}

export type CreateUserPayload = {
  name: string
  email: string
  password: string
  role: Role
  status: UserStatus
}

export type UpdateUserPayload = {
  name: string
  role: Role
  status: UserStatus
}

export type UserResponse = {
  data: {
    id: string
    tenant_id: string
    name: string
    email: string
    role: Role
    status: UserStatus
  }
}

export type UserListResponse = {
  data: UserResponse['data'][]
  meta: {
    total: number
  }
}

export type UserListResult = {
  items: WorkshopUser[]
  total: number
}

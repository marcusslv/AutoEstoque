import type { ApiClient } from '~/shared/api/apiClient'
import type {
  CreateUserPayload,
  UpdateUserPayload,
  UserFormValues,
  UserListResponse,
  UserListResult,
  UserResponse,
  WorkshopUser,
} from '../types/user'

const mapUser = (user: UserResponse['data']): WorkshopUser => ({
  id: user.id,
  tenantId: user.tenant_id,
  name: user.name,
  email: user.email,
  role: user.role,
  status: user.status,
})

const toCreatePayload = (values: UserFormValues): CreateUserPayload => ({
  name: values.name.trim(),
  email: values.email.trim().toLowerCase(),
  password: values.password,
  role: values.role,
  status: values.status,
})

const toUpdatePayload = (values: UserFormValues): UpdateUserPayload => ({
  name: values.name.trim(),
  role: values.role,
  status: values.status,
})

export const createUsersApi = (api: ApiClient) => {
  const listUsers = async (): Promise<UserListResult> => {
    const response = await api.get<UserListResponse>('/users')

    return {
      items: response.data.map(mapUser),
      total: response.meta.total,
    }
  }

  const createUser = async (values: UserFormValues) => {
    const response = await api.post<UserResponse, CreateUserPayload>('/users', toCreatePayload(values))

    return mapUser(response.data)
  }

  const updateUser = async (userId: string, values: UserFormValues) => {
    const response = await api.patch<UserResponse, UpdateUserPayload>(`/users/${userId}`, toUpdatePayload(values))

    return mapUser(response.data)
  }

  const deactivateUser = async (userId: string) => {
    const response = await api.patch<UserResponse>(`/users/${userId}/deactivate`)

    return mapUser(response.data)
  }

  return {
    listUsers,
    createUser,
    updateUser,
    deactivateUser,
  }
}

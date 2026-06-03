export type Role = 'owner' | 'manager' | 'admin' | 'mechanic'

export const roles: Role[] = ['owner', 'manager', 'admin', 'mechanic']

export const isRole = (value: unknown): value is Role => {
  return typeof value === 'string' && roles.includes(value as Role)
}

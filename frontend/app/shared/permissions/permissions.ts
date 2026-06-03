import type { Role } from './roles'

export type PermissionKey =
  | 'backoffice'
  | 'workshop'
  | 'dashboard'
  | 'catalog'
  | 'inventory'
  | 'manual_inventory'
  | 'users'
  | 'settings'

export const permissions: Record<PermissionKey, Role[]> = {
  backoffice: ['owner', 'manager', 'admin'],
  workshop: ['owner', 'manager', 'admin', 'mechanic'],
  dashboard: ['owner', 'manager', 'admin'],
  catalog: ['owner', 'manager', 'admin'],
  inventory: ['owner', 'manager', 'admin', 'mechanic'],
  manual_inventory: ['owner', 'manager', 'admin'],
  users: ['owner', 'manager', 'admin'],
  settings: ['owner', 'manager', 'admin'],
}

export const canAccess = (role: Role | null | undefined, permission: PermissionKey) => {
  if (!role || role == undefined) {
    return false
  }

  return permissions[permission].includes(role)
}

export const canAccessAny = (role: Role | null | undefined, permissionKeys: PermissionKey[]) => {
  return permissionKeys.some((permission) => canAccess(role, permission))
}

export const canAccessAll = (role: Role | null | undefined, permissionKeys: PermissionKey[]) => {
  return permissionKeys.every((permission) => canAccess(role, permission))
}

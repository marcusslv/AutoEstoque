import { canAccess, canAccessAll, canAccessAny } from './permissions'
import type { PermissionKey } from './permissions'
import { useAuthStore } from '~/modules/auth/stores/authStore'

export const usePermissions = () => {
  const authStore = useAuthStore()
  const role = computed(() => authStore.user?.role ?? null)

  return {
    role,
    canAccess: (permission: PermissionKey) => canAccess(role.value, permission),
    canAccessAny: (permissions: PermissionKey[]) => canAccessAny(role.value, permissions),
    canAccessAll: (permissions: PermissionKey[]) => canAccessAll(role.value, permissions),
  }
}

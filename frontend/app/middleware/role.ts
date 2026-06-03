import { useAuthStore } from '~/modules/auth/stores/authStore'
import { canAccess } from '~/shared/permissions/permissions'
import type { PermissionKey } from '~/shared/permissions/permissions'

export default defineNuxtRouteMiddleware((to) => {
  const authStore = useAuthStore()
  const permission = to.meta.permission as PermissionKey | undefined

  if (!permission) {
    return
  }

  if (!canAccess(authStore.user?.role, permission)) {
    return navigateTo('/forbidden')
  }
})

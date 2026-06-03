import { useAuthStore } from '~/modules/auth/stores/authStore'

export default defineNuxtRouteMiddleware(() => {
  const authStore = useAuthStore()

  if (authStore.isAuthenticated) {
    return navigateTo('/dashboard')
  }
})

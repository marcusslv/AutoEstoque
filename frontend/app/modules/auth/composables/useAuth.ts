import { useAuthStore } from '../stores/authStore'

export const useAuth = () => {
  const authStore = useAuthStore()

  return {
    authStore,
    user: computed(() => authStore.user),
    loading: computed(() => authStore.loading),
    isAuthenticated: computed(() => authStore.isAuthenticated),
    login: authStore.login,
    logout: authStore.logout,
    clear: authStore.clear,
  }
}

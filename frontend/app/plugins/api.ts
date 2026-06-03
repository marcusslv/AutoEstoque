import { createApiClient } from '~/shared/api/apiClient'
import { useAuthToken } from '~/shared/auth/authToken'
import { useApiErrorState } from '~/shared/errors/apiErrorState'

export default defineNuxtPlugin(() => {
  const config = useRuntimeConfig()
  const { token, clearToken } = useAuthToken()
  const { setForbidden, clearApiErrorState } = useApiErrorState()

  const api = createApiClient({
    baseUrl: config.public.apiBaseUrl,
    getToken: () => token.value,
    onUnauthorized: () => {
      clearToken()
      clearApiErrorState()
    },
    onForbidden: () => {
      setForbidden()
    },
  })

  return {
    provide: {
      api,
    },
  }
})

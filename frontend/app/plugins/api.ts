import { createApiClient } from '~/shared/api/apiClient'
import { useAuthSession } from '~/shared/auth/authToken'
import { useApiErrorState } from '~/shared/errors/apiErrorState'

export default defineNuxtPlugin(() => {
  const config = useRuntimeConfig()
  const { token, clearSession } = useAuthSession()
  const { setForbidden, clearApiErrorState } = useApiErrorState()

  const api = createApiClient({
    baseUrl: config.public.apiBaseUrl,
    getToken: () => token.value,
    onUnauthorized: () => {
      clearSession()
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

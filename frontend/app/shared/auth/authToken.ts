export const authTokenCookieName = 'autoestoque_access_token'

export const useAuthToken = () => {
  const token = useCookie<string | null>(authTokenCookieName, {
    sameSite: 'lax',
    watch: true,
  })

  const setToken = (value: string | null) => {
    token.value = value
  }

  const clearToken = () => {
    token.value = null
  }

  return {
    token,
    setToken,
    clearToken,
  }
}

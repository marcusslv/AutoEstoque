export type ApiErrorState = {
  statusCode?: number
  message?: string
  forbidden: boolean
}

export const useApiErrorState = () => {
  const state = useState<ApiErrorState>('api:error-state', () => ({
    forbidden: false,
  }))

  const setForbidden = (message = 'Seu perfil nao possui permissao para acessar este recurso.') => {
    state.value = {
      statusCode: 403,
      message,
      forbidden: true,
    }
  }

  const clearApiErrorState = () => {
    state.value = {
      forbidden: false,
    }
  }

  return {
    state,
    setForbidden,
    clearApiErrorState,
  }
}

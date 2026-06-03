import type { FetchError } from 'ofetch'
import type { ApiErrorResponse, ApiValidationErrors, ApiValidationErrorResponse } from './apiTypes'

export type ApiErrorKind =
  | 'unauthorized'
  | 'forbidden'
  | 'not_found'
  | 'conflict'
  | 'validation'
  | 'server'
  | 'network'
  | 'unknown'

export class ApiError extends Error {
  readonly kind: ApiErrorKind
  readonly statusCode?: number
  readonly errors?: ApiValidationErrors
  readonly payload?: unknown

  constructor(params: {
    kind: ApiErrorKind
    message: string
    statusCode?: number
    errors?: ApiValidationErrors
    payload?: unknown
  }) {
    super(params.message)
    this.name = 'ApiError'
    this.kind = params.kind
    this.statusCode = params.statusCode
    this.errors = params.errors
    this.payload = params.payload
  }

  get isUnauthorized() {
    return this.statusCode === 401
  }

  get isForbidden() {
    return this.statusCode === 403
  }

  get isValidation() {
    return this.statusCode === 422
  }
}

export const isApiError = (error: unknown): error is ApiError => {
  return error instanceof ApiError
}

export const normalizeApiError = (error: unknown): ApiError => {
  if (isApiError(error)) {
    return error
  }

  const fetchError = error as FetchError<ApiErrorResponse | ApiValidationErrorResponse>
  const statusCode = fetchError.response?.status ?? fetchError.statusCode
  const payload = fetchError.data
  const message = resolveErrorMessage(payload, fetchError.message)

  if (!statusCode) {
    return new ApiError({
      kind: 'network',
      message: 'Nao foi possivel conectar com a API.',
      payload,
    })
  }

  if (statusCode === 401) {
    return new ApiError({
      kind: 'unauthorized',
      statusCode,
      message: message || 'Sessao expirada. Entre novamente.',
      payload,
    })
  }

  if (statusCode === 403) {
    return new ApiError({
      kind: 'forbidden',
      statusCode,
      message: message || 'Seu perfil nao possui permissao para esta acao.',
      payload,
    })
  }

  if (statusCode === 404) {
    return new ApiError({
      kind: 'not_found',
      statusCode,
      message: message || 'Recurso nao encontrado.',
      payload,
    })
  }

  if (statusCode === 409) {
    return new ApiError({
      kind: 'conflict',
      statusCode,
      message: message || 'A operacao conflita com o estado atual do recurso.',
      payload,
    })
  }

  if (statusCode === 422) {
    const validationPayload = payload as ApiValidationErrorResponse | undefined

    return new ApiError({
      kind: 'validation',
      statusCode,
      message: message || 'Revise os campos informados.',
      errors: validationPayload?.errors ?? {},
      payload,
    })
  }

  if (statusCode >= 500) {
    return new ApiError({
      kind: 'server',
      statusCode,
      message: message || 'Erro interno da API. Tente novamente em instantes.',
      payload,
    })
  }

  return new ApiError({
    kind: 'unknown',
    statusCode,
    message: message || 'Nao foi possivel concluir a operacao.',
    payload,
  })
}

export const getApiErrorMessage = (error: unknown, fallback = 'Nao foi possivel concluir a operacao.') => {
  if (isApiError(error)) {
    return error.message || fallback
  }

  return normalizeApiError(error).message || fallback
}

export const getApiValidationErrors = (error: unknown): ApiValidationErrors => {
  if (isApiError(error)) {
    return error.errors ?? {}
  }

  return normalizeApiError(error).errors ?? {}
}

const resolveErrorMessage = (payload: unknown, fallback?: string) => {
  if (payload && typeof payload === 'object' && 'message' in payload) {
    const message = (payload as ApiErrorResponse).message

    if (message) {
      return message
    }
  }

  return fallback
}

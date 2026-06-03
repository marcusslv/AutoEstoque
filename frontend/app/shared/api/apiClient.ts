import { $fetch } from 'ofetch'
import type { FetchOptions } from 'ofetch'
import { normalizeApiError } from './apiErrors'
import type { ApiRequestOptions } from './apiTypes'

type ApiClientConfig = {
  baseUrl: string
  getToken?: () => string | null | undefined
  onUnauthorized?: () => void
  onForbidden?: () => void
}

export type ApiClient = {
  request: <TResponse, TBody = unknown>(path: string, options?: ApiRequestOptions<TBody>) => Promise<TResponse>
  get: <TResponse>(path: string, options?: Omit<ApiRequestOptions, 'method' | 'body'>) => Promise<TResponse>
  post: <TResponse, TBody = unknown>(path: string, body?: TBody, options?: Omit<ApiRequestOptions<TBody>, 'method' | 'body'>) => Promise<TResponse>
  put: <TResponse, TBody = unknown>(path: string, body?: TBody, options?: Omit<ApiRequestOptions<TBody>, 'method' | 'body'>) => Promise<TResponse>
  patch: <TResponse, TBody = unknown>(path: string, body?: TBody, options?: Omit<ApiRequestOptions<TBody>, 'method' | 'body'>) => Promise<TResponse>
  delete: <TResponse>(path: string, options?: Omit<ApiRequestOptions, 'method' | 'body'>) => Promise<TResponse>
}

export const createApiClient = (config: ApiClientConfig): ApiClient => {
  const fetcher = $fetch.create({
    baseURL: config.baseUrl,
    retry: 0,
    onRequest({ options }) {
      const token = config.getToken?.()

      if (!token) {
        return
      }

      const headers = new Headers(options.headers)
      headers.set('Authorization', `Bearer ${token}`)
      options.headers = headers
    },
    onResponseError({ response }) {
      if (response.status === 401) {
        config.onUnauthorized?.()
      }

      if (response.status === 403) {
        config.onForbidden?.()
      }
    },
  })

  const request = async <TResponse, TBody = unknown>(
    path: string,
    options: ApiRequestOptions<TBody> = {},
  ): Promise<TResponse> => {
    try {
      const fetchOptions: FetchOptions<'json'> = {
        method: options.method ?? 'GET',
        body: options.body as FetchOptions<'json'>['body'],
        query: options.query,
        headers: options.headers,
      }

      return await fetcher<TResponse>(path, fetchOptions)
    } catch (error) {
      throw normalizeApiError(error)
    }
  }

  return {
    request,
    get: (path, options) => request(path, { ...options, method: 'GET' }),
    post: (path, body, options) => request(path, { ...options, method: 'POST', body }),
    put: (path, body, options) => request(path, { ...options, method: 'PUT', body }),
    patch: (path, body, options) => request(path, { ...options, method: 'PATCH', body }),
    delete: (path, options) => request(path, { ...options, method: 'DELETE' }),
  }
}

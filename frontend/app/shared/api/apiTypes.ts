export type ApiResponse<T> = {
  data: T
}

export type ApiListResponse<T> = {
  data: T[]
  meta?: ApiMeta
}

export type ApiMeta = {
  total?: number
  per_page?: number
  current_page?: number
  last_page?: number
}

export type ApiMessageResponse = ApiResponse<{
  message: string
}>

export type ApiErrorResponse = {
  message: string
}

export type ApiValidationErrorResponse = ApiErrorResponse & {
  errors: ApiValidationErrors
}

export type ApiValidationErrors = Record<string, string[]>

export type ApiRequestMethod = 'GET' | 'POST' | 'PUT' | 'PATCH' | 'DELETE'

export type ApiRequestOptions<TBody = unknown> = {
  method?: ApiRequestMethod
  body?: TBody
  query?: Record<string, string | number | boolean | null | undefined>
  headers?: Record<string, string>
}

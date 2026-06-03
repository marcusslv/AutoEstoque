export type ApiResponse<T> = {
  data: T
}

export type ApiListResponse<T> = {
  data: T[]
  meta?: {
    total?: number
  }
}

export type Vehicle = {
  id: string
  tenantId: string
  plate: string
  brand: string
  model: string
  year: number
  ownerName: string
  ownerPhone: string
  createdAt?: string | null
}

export type VehicleFormValues = {
  plate: string
  brand: string
  model: string
  year: number
  ownerName: string
  ownerPhone: string
}

export type VehicleFilters = {
  search?: string
  limit?: number
}

export type VehiclePayload = {
  plate: string
  brand: string
  model: string
  year: number
  owner_name: string
  owner_phone: string
}

export type VehicleResponse = {
  data: {
    id: string
    tenant_id: string
    plate: string
    brand: string
    model: string
    year: number
    owner_name: string
    owner_phone: string
    created_at?: string | null
  }
}

export type VehicleListResponse = {
  data: VehicleResponse['data'][]
  meta: {
    total: number
  }
}

export type VehicleListResult = {
  items: Vehicle[]
  total: number
}

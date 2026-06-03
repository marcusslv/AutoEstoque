import type { Vehicle } from './vehicle'

export type ServiceOrderStatus = 'open' | 'finished'

export type ServiceOrderVehicle = Pick<Vehicle, 'id' | 'plate' | 'brand' | 'model' | 'ownerName'> & {
  year?: number
  ownerPhone?: string
}

export type ServiceOrderListItem = {
  id: string
  tenantId: string
  customerName: string
  servicesDescription: string
  observations: string | null
  status: ServiceOrderStatus
  openedAt: string
  finishedAt: string | null
  vehicle: ServiceOrderVehicle
  partsTotal: number
}

export type ServiceOrderPartMovement = {
  id: string
  direction: string
  type: string
  quantity: number
  reason: string
  note: string | null
  occurredAt: string
}

export type ServiceOrderPart = {
  id: string
  productId: string
  productName: string
  productSku: string
  addedByUserId: string
  quantity: number
  createdAt: string
  movements: ServiceOrderPartMovement[]
  movementsTotal: number
}

export type ServiceOrderDetails = ServiceOrderListItem & {
  createdByUserId: string
  vehicle: Required<ServiceOrderVehicle>
  parts: ServiceOrderPart[]
}

export type ServiceOrderFilters = {
  status?: ServiceOrderStatus | ''
  search?: string
  limit?: number
}

export type ServiceOrderFormValues = {
  vehicleId: string
  customerName: string
  servicesDescription: string
  observations: string
}

export type AddPartFormValues = {
  productId: string
  quantity: number
}

export type ServiceOrderListResponse = {
  data: Array<{
    id: string
    tenant_id: string
    customer_name: string
    services_description: string
    observations: string | null
    status: ServiceOrderStatus
    opened_at: string
    finished_at: string | null
    vehicle: {
      id: string
      plate: string
      brand: string
      model: string
      owner_name: string
    }
    parts_total: number
  }>
  meta: {
    total: number
  }
}

export type ServiceOrderDetailsResponse = {
  data: {
    id: string
    tenant_id: string
    created_by_user_id: string
    customer_name: string
    services_description: string
    observations: string | null
    status: ServiceOrderStatus
    opened_at: string
    finished_at: string | null
    vehicle: {
      id: string
      plate: string
      brand: string
      model: string
      year: number
      owner_name: string
      owner_phone: string
    }
    parts: Array<{
      id: string
      product_id: string
      product_name: string
      product_sku: string
      added_by_user_id: string
      quantity: number
      created_at: string
      movements: Array<{
        id: string
        direction: string
        type: string
        quantity: number
        reason: string
        note: string | null
        occurred_at: string
      }>
      movements_total: number
    }>
  }
  meta: {
    parts_total: number
  }
}

export type CreateServiceOrderPayload = {
  vehicle_id: string
  customer_name: string
  services_description: string
  observations?: string | null
}

export type AddPartPayload = {
  product_id: string
  quantity: number
}

export type FinishServiceOrderResponse = {
  data: {
    id: string
    tenant_id: string
    status: 'finished'
    finished_at: string
    movement_ids: string[]
  }
}

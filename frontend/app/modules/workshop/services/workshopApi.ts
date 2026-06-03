import type { ApiClient } from '~/shared/api/apiClient'
import type {
  AddPartFormValues,
  AddPartPayload,
  CreateServiceOrderPayload,
  FinishServiceOrderResponse,
  ServiceOrderDetails,
  ServiceOrderDetailsResponse,
  ServiceOrderFilters,
  ServiceOrderFormValues,
  ServiceOrderListItem,
  ServiceOrderListResponse,
} from '../types/serviceOrder'
import type {
  Vehicle,
  VehicleFilters,
  VehicleFormValues,
  VehicleListResponse,
  VehicleListResult,
  VehiclePayload,
  VehicleResponse,
} from '../types/vehicle'

const mapVehicle = (vehicle: VehicleResponse['data']): Vehicle => ({
  id: vehicle.id,
  tenantId: vehicle.tenant_id,
  plate: vehicle.plate,
  brand: vehicle.brand,
  model: vehicle.model,
  year: vehicle.year,
  ownerName: vehicle.owner_name,
  ownerPhone: vehicle.owner_phone,
  createdAt: vehicle.created_at ?? null,
})

const toVehiclePayload = (values: VehicleFormValues): VehiclePayload => ({
  plate: values.plate.trim().toUpperCase(),
  brand: values.brand.trim(),
  model: values.model.trim(),
  year: Number(values.year) || new Date().getFullYear(),
  owner_name: values.ownerName.trim(),
  owner_phone: values.ownerPhone.trim(),
})

const nullableString = (value: string) => {
  const normalizedValue = value.trim()

  return normalizedValue ? normalizedValue : null
}

const mapServiceOrderListItem = (serviceOrder: ServiceOrderListResponse['data'][number]): ServiceOrderListItem => ({
  id: serviceOrder.id,
  tenantId: serviceOrder.tenant_id,
  customerName: serviceOrder.customer_name,
  servicesDescription: serviceOrder.services_description,
  observations: serviceOrder.observations,
  status: serviceOrder.status,
  openedAt: serviceOrder.opened_at,
  finishedAt: serviceOrder.finished_at,
  vehicle: {
    id: serviceOrder.vehicle.id,
    plate: serviceOrder.vehicle.plate,
    brand: serviceOrder.vehicle.brand,
    model: serviceOrder.vehicle.model,
    ownerName: serviceOrder.vehicle.owner_name,
  },
  partsTotal: serviceOrder.parts_total,
})

const mapServiceOrderDetails = (response: ServiceOrderDetailsResponse): ServiceOrderDetails => ({
  id: response.data.id,
  tenantId: response.data.tenant_id,
  createdByUserId: response.data.created_by_user_id,
  customerName: response.data.customer_name,
  servicesDescription: response.data.services_description,
  observations: response.data.observations,
  status: response.data.status,
  openedAt: response.data.opened_at,
  finishedAt: response.data.finished_at,
  vehicle: {
    id: response.data.vehicle.id,
    plate: response.data.vehicle.plate,
    brand: response.data.vehicle.brand,
    model: response.data.vehicle.model,
    year: response.data.vehicle.year,
    ownerName: response.data.vehicle.owner_name,
    ownerPhone: response.data.vehicle.owner_phone,
  },
  partsTotal: response.meta.parts_total,
  parts: response.data.parts.map((part) => ({
    id: part.id,
    productId: part.product_id,
    productName: part.product_name,
    productSku: part.product_sku,
    addedByUserId: part.added_by_user_id,
    quantity: part.quantity,
    createdAt: part.created_at,
    movementsTotal: part.movements_total,
    movements: part.movements.map((movement) => ({
      id: movement.id,
      direction: movement.direction,
      type: movement.type,
      quantity: movement.quantity,
      reason: movement.reason,
      note: movement.note,
      occurredAt: movement.occurred_at,
    })),
  })),
})

const toServiceOrderPayload = (values: ServiceOrderFormValues): CreateServiceOrderPayload => ({
  vehicle_id: values.vehicleId,
  customer_name: values.customerName.trim(),
  services_description: values.servicesDescription.trim(),
  observations: nullableString(values.observations),
})

const toAddPartPayload = (values: AddPartFormValues): AddPartPayload => ({
  product_id: values.productId,
  quantity: Number(values.quantity) || 1,
})

export const createWorkshopApi = (api: ApiClient) => {
  const listVehicles = async (filters: VehicleFilters = {}): Promise<VehicleListResult> => {
    const response = await api.get<VehicleListResponse>('/vehicles', {
      query: {
        search: filters.search?.trim() || undefined,
        limit: filters.limit ?? 50,
      },
    })

    return {
      items: response.data.map(mapVehicle),
      total: response.meta.total,
    }
  }

  const createVehicle = async (values: VehicleFormValues) => {
    const response = await api.post<VehicleResponse, VehiclePayload>('/vehicles', toVehiclePayload(values))

    return mapVehicle(response.data)
  }

  const listServiceOrders = async (filters: ServiceOrderFilters = {}) => {
    const response = await api.get<ServiceOrderListResponse>('/service-orders', {
      query: {
        status: filters.status || undefined,
        search: filters.search?.trim() || undefined,
        limit: filters.limit ?? 50,
      },
    })

    return {
      items: response.data.map(mapServiceOrderListItem),
      total: response.meta.total,
    }
  }

  const createServiceOrder = async (values: ServiceOrderFormValues) => {
    return api.post('/service-orders', toServiceOrderPayload(values))
  }

  const getServiceOrderDetails = async (serviceOrderId: string) => {
    const response = await api.get<ServiceOrderDetailsResponse>(`/service-orders/${serviceOrderId}`)

    return mapServiceOrderDetails(response)
  }

  const addPartToServiceOrder = async (serviceOrderId: string, values: AddPartFormValues) => {
    return api.post(`/service-orders/${serviceOrderId}/parts`, toAddPartPayload(values))
  }

  const finishServiceOrder = async (serviceOrderId: string) => {
    return api.patch<FinishServiceOrderResponse>(`/service-orders/${serviceOrderId}/finish`)
  }

  return {
    listVehicles,
    createVehicle,
    listServiceOrders,
    createServiceOrder,
    getServiceOrderDetails,
    addPartToServiceOrder,
    finishServiceOrder,
  }
}

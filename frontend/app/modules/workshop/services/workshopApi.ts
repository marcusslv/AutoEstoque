import type { ApiClient } from '~/shared/api/apiClient'
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

  return {
    listVehicles,
    createVehicle,
  }
}

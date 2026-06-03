export type Product = {
  id: string
  tenantId: string
  name: string
  sku: string
  barcode: string | null
  category: string | null
  brand: string | null
  supplier: string | null
  minimumStock: number
  costInCents: number
  currency: string
}

export type ProductFormValues = {
  name: string
  sku: string
  barcode: string
  category: string
  brand: string
  supplier: string
  minimumStock: number
  costInCents: number
  currency: string
}

export type ProductPayload = {
  name: string
  sku: string
  barcode?: string | null
  category?: string | null
  brand?: string | null
  supplier?: string | null
  minimum_stock: number
  cost_in_cents: number
  currency: string
}

export type ProductResponse = {
  data: {
    id: string
    tenant_id: string
    name: string
    sku: string
    barcode: string | null
    category: string | null
    brand: string | null
    supplier: string | null
    minimum_stock: number
    cost_in_cents: number
    currency: string
  }
}

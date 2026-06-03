<script setup lang="ts">
import type { ApiValidationErrors } from '~/shared/api/apiTypes'
import ProductForm from './ProductForm.vue'
import type { ProductFormValues } from '../types/product'
import type { StockItem } from '../types/stock'

const props = defineProps<{
  open: boolean
  product?: StockItem | null
  submitting?: boolean
  errors?: ApiValidationErrors
}>()

const emit = defineEmits<{
  close: []
  submit: [values: ProductFormValues]
}>()

const emptyForm = (): ProductFormValues => ({
  name: '',
  sku: '',
  barcode: '',
  category: '',
  brand: '',
  supplier: '',
  minimumStock: 0,
  costInCents: 0,
  currency: 'BRL',
})

const form = ref<ProductFormValues>(emptyForm())

watch(
  () => [props.open, props.product] as const,
  ([open, product]) => {
    if (!open) {
      return
    }

    form.value = product
      ? {
          name: product.name,
          sku: product.sku,
          barcode: product.barcode ?? '',
          category: product.category ?? '',
          brand: product.brand ?? '',
          supplier: product.supplier ?? '',
          minimumStock: product.minimumStock,
          costInCents: product.costInCents,
          currency: product.currency,
        }
      : emptyForm()
  },
  { immediate: true },
)
</script>

<template>
  <EntityFormDialog
    :open="props.open"
    :title="props.product ? 'Editar produto' : 'Novo produto'"
    :description="props.product ? 'Atualize os dados cadastrais da peca.' : 'Cadastre uma peca para controlar no estoque.'"
    :submit-label="props.product ? 'Salvar alteracoes' : 'Cadastrar produto'"
    :submitting="props.submitting"
    @close="$emit('close')"
    @submit="$emit('submit', form)"
  >
    <ProductForm
      v-model="form"
      :errors="props.errors"
    />
  </EntityFormDialog>
</template>

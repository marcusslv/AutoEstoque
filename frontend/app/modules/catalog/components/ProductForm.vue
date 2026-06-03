<script setup lang="ts">
import type { ApiValidationErrors } from '~/shared/api/apiTypes'
import type { ProductFormValues } from '../types/product'

const props = defineProps<{
  modelValue: ProductFormValues
  errors?: ApiValidationErrors
}>()

const emit = defineEmits<{
  'update:modelValue': [value: ProductFormValues]
}>()

const updateField = <TKey extends keyof ProductFormValues>(field: TKey, value: ProductFormValues[TKey]) => {
  emit('update:modelValue', {
    ...props.modelValue,
    [field]: value,
  })
}

const fieldError = (field: string) => props.errors?.[field]?.[0]

const costInReais = computed({
  get: () => (props.modelValue.costInCents / 100).toFixed(2),
  set: (value: string) => {
    const normalizedValue = Number(value.replace(',', '.'))
    updateField('costInCents', Number.isFinite(normalizedValue) ? Math.round(normalizedValue * 100) : 0)
  },
})
</script>

<template>
  <div class="grid gap-4 sm:grid-cols-2">
    <FormField
      label="Nome"
      required
      :error="fieldError('name')"
    >
      <AppInput
        :model-value="props.modelValue.name"
        autocomplete="off"
        placeholder="Filtro de oleo"
        @update:model-value="updateField('name', $event)"
      />
    </FormField>

    <FormField
      label="SKU"
      required
      :error="fieldError('sku')"
    >
      <AppInput
        :model-value="props.modelValue.sku"
        autocomplete="off"
        placeholder="FO-001"
        @update:model-value="updateField('sku', $event)"
      />
    </FormField>

    <FormField label="Codigo de barras" :error="fieldError('barcode')">
      <AppInput
        :model-value="props.modelValue.barcode"
        autocomplete="off"
        placeholder="7891234567890"
        @update:model-value="updateField('barcode', $event)"
      />
    </FormField>

    <FormField label="Categoria" :error="fieldError('category')">
      <AppInput
        :model-value="props.modelValue.category"
        autocomplete="off"
        placeholder="Filtros"
        @update:model-value="updateField('category', $event)"
      />
    </FormField>

    <FormField label="Marca" :error="fieldError('brand')">
      <AppInput
        :model-value="props.modelValue.brand"
        autocomplete="off"
        placeholder="Mann"
        @update:model-value="updateField('brand', $event)"
      />
    </FormField>

    <FormField label="Fornecedor" :error="fieldError('supplier')">
      <AppInput
        :model-value="props.modelValue.supplier"
        autocomplete="off"
        placeholder="Auto Pecas Central"
        @update:model-value="updateField('supplier', $event)"
      />
    </FormField>

    <FormField
      label="Estoque minimo"
      :error="fieldError('minimum_stock')"
    >
      <AppInput
        :model-value="props.modelValue.minimumStock"
        type="number"
        @update:model-value="updateField('minimumStock', Number($event) || 0)"
      />
    </FormField>

    <FormField
      label="Custo"
      required
      :error="fieldError('cost_in_cents')"
    >
      <AppInput
        v-model="costInReais"
        type="number"
        placeholder="25.90"
      />
    </FormField>
  </div>
</template>

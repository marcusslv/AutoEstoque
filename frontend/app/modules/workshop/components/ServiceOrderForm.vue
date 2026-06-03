<script setup lang="ts">
import type { AppSelectOption } from '~/components/ui/atoms/AppSelect.vue'
import type { ApiValidationErrors } from '~/shared/api/apiTypes'
import type { ServiceOrderFormValues } from '../types/serviceOrder'

const props = defineProps<{
  modelValue: ServiceOrderFormValues
  vehicleOptions: AppSelectOption[]
  errors?: ApiValidationErrors
}>()

const emit = defineEmits<{
  'update:modelValue': [value: ServiceOrderFormValues]
}>()

const updateField = <TKey extends keyof ServiceOrderFormValues>(field: TKey, value: ServiceOrderFormValues[TKey]) => {
  emit('update:modelValue', {
    ...props.modelValue,
    [field]: value,
  })
}

const fieldError = (field: string) => props.errors?.[field]?.[0]
</script>

<template>
  <div class="space-y-4">
    <FormField
      label="Veiculo"
      required
      :error="fieldError('vehicle_id')"
    >
      <AppSelect
        :model-value="props.modelValue.vehicleId"
        :options="props.vehicleOptions"
        placeholder="Selecione um veiculo"
        @update:model-value="updateField('vehicleId', $event)"
      />
    </FormField>

    <FormField
      label="Cliente"
      required
      :error="fieldError('customer_name')"
    >
      <AppInput
        :model-value="props.modelValue.customerName"
        autocomplete="off"
        placeholder="Joao Silva"
        @update:model-value="updateField('customerName', $event)"
      />
    </FormField>

    <FormField
      label="Servicos realizados"
      required
      :error="fieldError('services_description')"
    >
      <AppTextarea
        :model-value="props.modelValue.servicesDescription"
        placeholder="Troca de oleo e filtros"
        @update:model-value="updateField('servicesDescription', $event)"
      />
    </FormField>

    <FormField label="Observacoes" :error="fieldError('observations')">
      <AppTextarea
        :model-value="props.modelValue.observations"
        placeholder="Cliente aguardando"
        @update:model-value="updateField('observations', $event)"
      />
    </FormField>
  </div>
</template>

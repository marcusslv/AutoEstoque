<script setup lang="ts">
import type { ApiValidationErrors } from '~/shared/api/apiTypes'
import type { VehicleFormValues } from '../types/vehicle'

const props = defineProps<{
  modelValue: VehicleFormValues
  errors?: ApiValidationErrors
}>()

const emit = defineEmits<{
  'update:modelValue': [value: VehicleFormValues]
}>()

const updateField = <TKey extends keyof VehicleFormValues>(field: TKey, value: VehicleFormValues[TKey]) => {
  emit('update:modelValue', {
    ...props.modelValue,
    [field]: value,
  })
}

const fieldError = (field: string) => props.errors?.[field]?.[0]
</script>

<template>
  <div class="grid gap-4 sm:grid-cols-2">
    <FormField
      label="Placa"
      required
      :error="fieldError('plate')"
    >
      <AppInput
        :model-value="props.modelValue.plate"
        autocomplete="off"
        placeholder="ABC-1D23"
        @update:model-value="updateField('plate', $event.toUpperCase())"
      />
    </FormField>

    <FormField
      label="Ano"
      required
      :error="fieldError('year')"
    >
      <AppInput
        :model-value="props.modelValue.year"
        type="number"
        @update:model-value="updateField('year', Number($event) || new Date().getFullYear())"
      />
    </FormField>

    <FormField
      label="Marca"
      required
      :error="fieldError('brand')"
    >
      <AppInput
        :model-value="props.modelValue.brand"
        autocomplete="off"
        placeholder="Chevrolet"
        @update:model-value="updateField('brand', $event)"
      />
    </FormField>

    <FormField
      label="Modelo"
      required
      :error="fieldError('model')"
    >
      <AppInput
        :model-value="props.modelValue.model"
        autocomplete="off"
        placeholder="Onix"
        @update:model-value="updateField('model', $event)"
      />
    </FormField>

    <FormField
      label="Proprietario"
      required
      :error="fieldError('owner_name')"
    >
      <AppInput
        :model-value="props.modelValue.ownerName"
        autocomplete="off"
        placeholder="Joao Silva"
        @update:model-value="updateField('ownerName', $event)"
      />
    </FormField>

    <FormField
      label="Telefone"
      required
      :error="fieldError('owner_phone')"
    >
      <AppInput
        :model-value="props.modelValue.ownerPhone"
        autocomplete="off"
        placeholder="11999990000"
        @update:model-value="updateField('ownerPhone', $event)"
      />
    </FormField>
  </div>
</template>

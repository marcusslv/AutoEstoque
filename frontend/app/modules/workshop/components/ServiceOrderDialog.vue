<script setup lang="ts">
import type { AppSelectOption } from '~/components/ui/atoms/AppSelect.vue'
import type { ApiValidationErrors } from '~/shared/api/apiTypes'
import ServiceOrderForm from './ServiceOrderForm.vue'
import type { ServiceOrderFormValues } from '../types/serviceOrder'

const props = defineProps<{
  open: boolean
  vehicleOptions: AppSelectOption[]
  submitting?: boolean
  errors?: ApiValidationErrors
}>()

const emit = defineEmits<{
  close: []
  submit: [values: ServiceOrderFormValues]
}>()

const emptyForm = (): ServiceOrderFormValues => ({
  vehicleId: '',
  customerName: '',
  servicesDescription: '',
  observations: '',
})

const form = ref<ServiceOrderFormValues>(emptyForm())

watch(
  () => props.open,
  (open) => {
    if (open) {
      form.value = emptyForm()
    }
  },
  { immediate: true },
)
</script>

<template>
  <EntityFormDialog
    :open="props.open"
    title="Nova ordem de servico"
    description="Abra uma OS para registrar servicos e pecas utilizadas."
    submit-label="Criar OS"
    :submitting="props.submitting"
    @close="$emit('close')"
    @submit="$emit('submit', form)"
  >
    <ServiceOrderForm
      v-model="form"
      :vehicle-options="props.vehicleOptions"
      :errors="props.errors"
    />
  </EntityFormDialog>
</template>

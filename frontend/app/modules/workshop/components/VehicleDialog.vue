<script setup lang="ts">
import type { ApiValidationErrors } from '~/shared/api/apiTypes'
import VehicleForm from './VehicleForm.vue'
import type { VehicleFormValues } from '../types/vehicle'

const props = defineProps<{
  open: boolean
  submitting?: boolean
  errors?: ApiValidationErrors
}>()

const emit = defineEmits<{
  close: []
  submit: [values: VehicleFormValues]
}>()

const emptyForm = (): VehicleFormValues => ({
  plate: '',
  brand: '',
  model: '',
  year: new Date().getFullYear(),
  ownerName: '',
  ownerPhone: '',
})

const form = ref<VehicleFormValues>(emptyForm())

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
    title="Novo veiculo"
    description="Cadastre o veiculo para vincular ordens de servico."
    submit-label="Cadastrar veiculo"
    :submitting="props.submitting"
    @close="$emit('close')"
    @submit="$emit('submit', form)"
  >
    <VehicleForm
      v-model="form"
      :errors="props.errors"
    />
  </EntityFormDialog>
</template>

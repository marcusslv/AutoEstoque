<script setup lang="ts">
import type { AppSelectOption } from '~/components/ui/atoms/AppSelect.vue'
import type { ApiValidationErrors } from '~/shared/api/apiTypes'
import type { AddPartFormValues } from '../types/serviceOrder'

const props = defineProps<{
  open: boolean
  productOptions: AppSelectOption[]
  submitting?: boolean
  errors?: ApiValidationErrors
}>()

const emit = defineEmits<{
  close: []
  submit: [values: AddPartFormValues]
}>()

const form = ref<AddPartFormValues>({
  productId: '',
  quantity: 1,
})

const fieldError = (field: string) => props.errors?.[field]?.[0]

watch(
  () => props.open,
  (open) => {
    if (open) {
      form.value = {
        productId: '',
        quantity: 1,
      }
    }
  },
)
</script>

<template>
  <EntityFormDialog
    :open="props.open"
    title="Adicionar peca"
    description="Selecione uma peca disponivel e informe a quantidade usada."
    submit-label="Adicionar"
    :submitting="props.submitting"
    @close="$emit('close')"
    @submit="$emit('submit', form)"
  >
    <div class="space-y-4">
      <FormField
        label="Peca"
        required
        :error="fieldError('product_id')"
      >
        <AppSelect
          v-model="form.productId"
          :options="props.productOptions"
          placeholder="Selecione uma peca"
        />
      </FormField>

      <FormField
        label="Quantidade"
        required
        :error="fieldError('quantity')"
      >
        <AppInput
          :model-value="form.quantity"
          type="number"
          @update:model-value="form.quantity = Number($event) || 1"
        />
      </FormField>
    </div>
  </EntityFormDialog>
</template>

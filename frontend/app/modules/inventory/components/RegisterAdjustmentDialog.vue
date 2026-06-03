<script setup lang="ts">
import type { AppSelectOption } from '~/components/ui/atoms/AppSelect.vue'
import type { ApiValidationErrors } from '~/shared/api/apiTypes'
import type { StockMovementDirection, StockMovementFormValues } from '../types/movement'

const props = defineProps<{
  open: boolean
  productOptions: AppSelectOption[]
  submitting?: boolean
  errors?: ApiValidationErrors
}>()

const emit = defineEmits<{
  close: []
  submit: [values: StockMovementFormValues]
}>()

const directionOptions: AppSelectOption[] = [
  { label: 'Entrada', value: 'entry' },
  { label: 'Saida', value: 'output' },
]

const emptyForm = (): StockMovementFormValues => ({
  productId: '',
  direction: 'entry',
  type: 'manual_adjustment',
  quantity: 1,
  reason: '',
  note: '',
  unitCostInCents: null,
})

const form = ref<StockMovementFormValues>(emptyForm())

const fieldError = (field: string) => props.errors?.[field]?.[0]

watch(
  () => props.open,
  (open) => {
    if (open) {
      form.value = emptyForm()
    }
  },
)
</script>

<template>
  <EntityFormDialog
    :open="props.open"
    title="Registrar ajuste"
    description="Use ajustes para corrigir divergencias encontradas no inventario."
    submit-label="Registrar ajuste"
    :submitting="props.submitting"
    @close="$emit('close')"
    @submit="$emit('submit', form)"
  >
    <div class="space-y-4">
      <FormField label="Peca" required :error="fieldError('product_id')">
        <AppSelect
          v-model="form.productId"
          :options="props.productOptions"
          placeholder="Selecione uma peca"
        />
      </FormField>

      <div class="grid gap-4 sm:grid-cols-2">
        <FormField label="Direcao" required :error="fieldError('direction')">
          <AppSelect
            :model-value="form.direction"
            :options="directionOptions"
            @update:model-value="form.direction = $event as StockMovementDirection"
          />
        </FormField>

        <FormField label="Quantidade" required :error="fieldError('quantity')">
          <AppInput
            :model-value="form.quantity"
            type="number"
            @update:model-value="form.quantity = Number($event) || 1"
          />
        </FormField>
      </div>

      <FormField label="Motivo" required :error="fieldError('reason')">
        <AppInput
          v-model="form.reason"
          autocomplete="off"
          placeholder="Conferencia de estoque"
        />
      </FormField>

      <FormField label="Observacao" :error="fieldError('note')">
        <AppTextarea
          v-model="form.note"
          :rows="3"
          placeholder="Detalhes do inventario ou justificativa"
        />
      </FormField>
    </div>
  </EntityFormDialog>
</template>

<script setup lang="ts">
import type { AppSelectOption } from '~/components/ui/atoms/AppSelect.vue'
import type { ApiValidationErrors } from '~/shared/api/apiTypes'
import type { StockEntryType, StockMovementFormValues } from '../types/movement'

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

const typeOptions: AppSelectOption[] = [
  { label: 'Compra', value: 'purchase' },
  { label: 'Devolucao', value: 'return' },
  { label: 'Ajuste manual', value: 'manual_adjustment' },
]

const emptyForm = (): StockMovementFormValues => ({
  productId: '',
  direction: 'entry',
  type: 'purchase',
  quantity: 1,
  reason: '',
  note: '',
  unitCostInCents: null,
})

const form = ref<StockMovementFormValues>(emptyForm())

const fieldError = (field: string) => props.errors?.[field]?.[0]

const unitCostInReais = computed({
  get: () => (form.value.unitCostInCents === null ? '' : (form.value.unitCostInCents / 100).toFixed(2)),
  set: (value: string) => {
    const normalizedValue = Number(value.replace(',', '.'))
    form.value.unitCostInCents = Number.isFinite(normalizedValue) ? Math.round(normalizedValue * 100) : null
  },
})

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
    title="Registrar entrada"
    description="Informe a peca, quantidade e motivo da entrada no estoque."
    submit-label="Registrar entrada"
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
        <FormField label="Tipo" required :error="fieldError('type')">
          <AppSelect
            :model-value="form.type"
            :options="typeOptions"
            @update:model-value="form.type = $event as StockEntryType"
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

      <FormField label="Custo unitario" :error="fieldError('unit_cost_in_cents')">
        <AppInput
          v-model="unitCostInReais"
          type="number"
          placeholder="25.90"
        />
      </FormField>

      <FormField label="Motivo" required :error="fieldError('reason')">
        <AppInput
          v-model="form.reason"
          autocomplete="off"
          placeholder="Compra de reposicao"
        />
      </FormField>

      <FormField label="Observacao" :error="fieldError('note')">
        <AppTextarea
          v-model="form.note"
          :rows="3"
          placeholder="Nota fiscal, fornecedor ou contexto da entrada"
        />
      </FormField>
    </div>
  </EntityFormDialog>
</template>

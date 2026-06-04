<script setup lang="ts">
import type { AppSelectOption } from '~/components/ui/atoms/AppSelect.vue'
import type { ApiValidationErrors } from '~/shared/api/apiTypes'
import { maskPhone, onlyDigits } from '~/shared/utils/masks'
import type { WorkshopSettingsFormValues } from '../types/workshopSettings'
import SettingsToggle from './SettingsToggle.vue'

const props = defineProps<{
  modelValue: WorkshopSettingsFormValues
  errors?: ApiValidationErrors
  disabled?: boolean
}>()

const emit = defineEmits<{
  'update:modelValue': [value: WorkshopSettingsFormValues]
}>()

const timezoneOptions: AppSelectOption[] = [
  { label: 'America/Sao_Paulo', value: 'America/Sao_Paulo' },
]

const currencyOptions: AppSelectOption[] = [
  { label: 'BRL', value: 'BRL' },
]

const updateField = <TKey extends keyof WorkshopSettingsFormValues>(
  field: TKey,
  value: WorkshopSettingsFormValues[TKey],
) => {
  emit('update:modelValue', {
    ...props.modelValue,
    [field]: value,
  })
}

const updatePhoneField = (
  field: 'phone' | 'notificationPhone',
  value: string,
) => updateField(field, maskPhone(value))

const updateDocument = (value: string) => {
  updateField('document', onlyDigits(value).slice(0, 14))
}

const updateMinimumStockDefault = (value: string) => {
  updateField('minimumStockDefault', Math.max(0, Number(value || 0)))
}

const fieldError = (field: string) => props.errors?.[field]?.[0]
</script>

<template>
  <div class="space-y-5">
    <section class="space-y-4 rounded-lg border bg-card p-4">
      <div>
        <h2 class="text-sm font-medium">
          Dados da oficina
        </h2>
        <p class="mt-1 text-xs text-muted-foreground">
          Informacoes exibidas no painel e usadas nos contatos operacionais.
        </p>
      </div>

      <div class="grid gap-4 lg:grid-cols-2">
        <FormField label="Nome exibido" required :error="fieldError('display_name')">
          <AppInput
            :model-value="props.modelValue.displayName"
            :disabled="disabled"
            autocomplete="organization"
            placeholder="AutoEstoque Oficina Demo"
            @update:model-value="updateField('displayName', $event)"
          />
        </FormField>

        <FormField label="Razao social" :error="fieldError('legal_name')">
          <AppInput
            :model-value="props.modelValue.legalName"
            :disabled="disabled"
            autocomplete="organization-title"
            placeholder="Oficina Demo LTDA"
            @update:model-value="updateField('legalName', $event)"
          />
        </FormField>

        <FormField label="Documento" :error="fieldError('document')">
          <AppInput
            :model-value="props.modelValue.document"
            :disabled="disabled"
            inputmode="numeric"
            placeholder="12345678000190"
            @update:model-value="updateDocument"
          />
        </FormField>

        <FormField label="Telefone" :error="fieldError('phone')">
          <AppInput
            :model-value="props.modelValue.phone"
            :disabled="disabled"
            autocomplete="tel"
            placeholder="(11) 99999-0000"
            @update:model-value="updatePhoneField('phone', $event)"
          />
        </FormField>

        <FormField label="E-mail" :error="fieldError('email')">
          <AppInput
            :model-value="props.modelValue.email"
            :disabled="disabled"
            type="email"
            autocomplete="email"
            placeholder="contato@oficina.com"
            @update:model-value="updateField('email', $event)"
          />
        </FormField>

        <FormField label="Endereco" :error="fieldError('address')">
          <AppInput
            :model-value="props.modelValue.address"
            :disabled="disabled"
            autocomplete="street-address"
            placeholder="Rua Central, 100"
            @update:model-value="updateField('address', $event)"
          />
        </FormField>
      </div>
    </section>

    <section class="space-y-4 rounded-lg border bg-card p-4">
      <div>
        <h2 class="text-sm font-medium">
          Operacao
        </h2>
        <p class="mt-1 text-xs text-muted-foreground">
          Parametros aplicados aos proximos movimentos e ordens de servico.
        </p>
      </div>

      <div class="grid gap-4 lg:grid-cols-3">
        <FormField label="Fuso horario" required :error="fieldError('timezone')">
          <AppSelect
            :model-value="props.modelValue.timezone"
            :disabled="disabled"
            :options="timezoneOptions"
            @update:model-value="updateField('timezone', $event)"
          />
        </FormField>

        <FormField label="Moeda" required :error="fieldError('currency')">
          <AppSelect
            :model-value="props.modelValue.currency"
            :disabled="disabled"
            :options="currencyOptions"
            @update:model-value="updateField('currency', $event)"
          />
        </FormField>

        <FormField label="Estoque minimo padrao" required :error="fieldError('minimum_stock_default')">
          <AppInput
            :model-value="props.modelValue.minimumStockDefault"
            :disabled="disabled"
            type="number"
            placeholder="0"
            @update:model-value="updateMinimumStockDefault"
          />
        </FormField>
      </div>

      <div class="grid gap-3 lg:grid-cols-2">
        <SettingsToggle
          :model-value="props.modelValue.allowNegativeStock"
          :disabled="disabled"
          label="Permitir estoque negativo"
          description="Quando desligado, saidas que deixariam saldo negativo devem ser bloqueadas."
          @update:model-value="updateField('allowNegativeStock', $event)"
        />

        <SettingsToggle
          :model-value="props.modelValue.autoDeductStockOnServiceOrderFinish"
          :disabled="disabled"
          label="Baixa automatica ao finalizar OS"
          description="Ao finalizar uma ordem de servico, as pecas vinculadas geram saidas de estoque."
          @update:model-value="updateField('autoDeductStockOnServiceOrderFinish', $event)"
        />
      </div>
    </section>

    <section class="space-y-4 rounded-lg border bg-card p-4">
      <div>
        <h2 class="text-sm font-medium">
          Notificacoes
        </h2>
        <p class="mt-1 text-xs text-muted-foreground">
          Preferencias para alertas de reposicao e estoque zerado.
        </p>
      </div>

      <div class="grid gap-3 lg:grid-cols-2">
        <SettingsToggle
          :model-value="props.modelValue.notifyMinimumStock"
          :disabled="disabled"
          label="Alertar estoque abaixo do minimo"
          @update:model-value="updateField('notifyMinimumStock', $event)"
        />

        <SettingsToggle
          :model-value="props.modelValue.notifyZeroStock"
          :disabled="disabled"
          label="Alertar estoque zerado"
          @update:model-value="updateField('notifyZeroStock', $event)"
        />
      </div>

      <div class="grid gap-4 lg:grid-cols-2">
        <FormField label="E-mail para notificacoes" :error="fieldError('notification_email')">
          <AppInput
            :model-value="props.modelValue.notificationEmail"
            :disabled="disabled"
            type="email"
            placeholder="alertas@oficina.com"
            @update:model-value="updateField('notificationEmail', $event)"
          />
        </FormField>

        <FormField label="Telefone para notificacoes" :error="fieldError('notification_phone')">
          <AppInput
            :model-value="props.modelValue.notificationPhone"
            :disabled="disabled"
            placeholder="(11) 98888-7777"
            @update:model-value="updatePhoneField('notificationPhone', $event)"
          />
        </FormField>
      </div>
    </section>
  </div>
</template>

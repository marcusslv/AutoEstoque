<script setup lang="ts">
import type { AppSelectOption } from '~/components/ui/atoms/AppSelect.vue'
import type { ApiValidationErrors } from '~/shared/api/apiTypes'
import type { Role } from '~/shared/permissions/roles'
import type { UserFormValues, UserStatus } from '../types/user'

const props = defineProps<{
  modelValue: UserFormValues
  editing?: boolean
  errors?: ApiValidationErrors
}>()

const emit = defineEmits<{
  'update:modelValue': [value: UserFormValues]
}>()

const roleOptions: AppSelectOption[] = [
  { label: 'Proprietario', value: 'owner' },
  { label: 'Gerente', value: 'manager' },
  { label: 'Administrador', value: 'admin' },
  { label: 'Mecanico', value: 'mechanic' },
]

const statusOptions: AppSelectOption[] = [
  { label: 'Ativo', value: 'active' },
  { label: 'Inativo', value: 'inactive' },
]

const updateField = <TKey extends keyof UserFormValues>(field: TKey, value: UserFormValues[TKey]) => {
  emit('update:modelValue', {
    ...props.modelValue,
    [field]: value,
  })
}

const fieldError = (field: string) => props.errors?.[field]?.[0]
</script>

<template>
  <div class="space-y-4">
    <FormField label="Nome" required :error="fieldError('name')">
      <AppInput
        :model-value="props.modelValue.name"
        autocomplete="off"
        placeholder="Maria Silva"
        @update:model-value="updateField('name', $event)"
      />
    </FormField>

    <FormField label="E-mail" required :error="fieldError('email')">
      <AppInput
        :model-value="props.modelValue.email"
        type="email"
        autocomplete="off"
        :disabled="props.editing"
        placeholder="maria@oficina.com"
        @update:model-value="updateField('email', $event)"
      />
    </FormField>

    <FormField
      v-if="!props.editing"
      label="Senha"
      required
      :error="fieldError('password')"
    >
      <AppInput
        :model-value="props.modelValue.password"
        type="password"
        autocomplete="new-password"
        placeholder="Minimo 8 caracteres"
        @update:model-value="updateField('password', $event)"
      />
    </FormField>

    <div class="grid gap-4 sm:grid-cols-2">
      <FormField label="Perfil" required :error="fieldError('role')">
        <AppSelect
          :model-value="props.modelValue.role"
          :options="roleOptions"
          @update:model-value="updateField('role', $event as Role)"
        />
      </FormField>

      <FormField label="Status" required :error="fieldError('status')">
        <AppSelect
          :model-value="props.modelValue.status"
          :options="statusOptions"
          @update:model-value="updateField('status', $event as UserStatus)"
        />
      </FormField>
    </div>
  </div>
</template>

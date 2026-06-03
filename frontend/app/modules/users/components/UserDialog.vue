<script setup lang="ts">
import type { ApiValidationErrors } from '~/shared/api/apiTypes'
import UserForm from './UserForm.vue'
import type { UserFormValues, WorkshopUser } from '../types/user'

const props = defineProps<{
  open: boolean
  user?: WorkshopUser | null
  submitting?: boolean
  errors?: ApiValidationErrors
}>()

const emit = defineEmits<{
  close: []
  submit: [values: UserFormValues]
}>()

const emptyForm = (): UserFormValues => ({
  name: '',
  email: '',
  password: '',
  role: 'mechanic',
  status: 'active',
})

const form = ref<UserFormValues>(emptyForm())

watch(
  () => [props.open, props.user] as const,
  ([open, user]) => {
    if (!open) {
      return
    }

    form.value = user
      ? {
          name: user.name,
          email: user.email,
          password: '',
          role: user.role,
          status: user.status,
        }
      : emptyForm()
  },
  { immediate: true },
)
</script>

<template>
  <EntityFormDialog
    :open="props.open"
    :title="props.user ? 'Editar usuario' : 'Novo usuario'"
    :description="props.user ? 'Atualize perfil e status do usuario.' : 'Crie um acesso para a equipe da oficina.'"
    :submit-label="props.user ? 'Salvar alteracoes' : 'Cadastrar usuario'"
    :submitting="props.submitting"
    @close="$emit('close')"
    @submit="$emit('submit', form)"
  >
    <UserForm
      v-model="form"
      :editing="Boolean(props.user)"
      :errors="props.errors"
    />
  </EntityFormDialog>
</template>

<script setup lang="ts">
import { getApiErrorMessage, getApiValidationErrors } from '~/shared/api/apiErrors'
import { useAuth } from '../composables/useAuth'

const route = useRoute()
const { login, loading } = useAuth()

const email = ref('owner@autoestoque.test')
const password = ref('password')
const formError = ref('')
const fieldErrors = ref<Record<string, string[]>>({})

const redirectTo = computed(() => {
  const redirect = route.query.redirect

  return typeof redirect === 'string' && redirect.startsWith('/')
    ? redirect
    : '/dashboard'
})

const submit = async () => {
  formError.value = ''
  fieldErrors.value = {}

  try {
    await login({
      email: email.value,
      password: password.value,
    })

    await navigateTo(redirectTo.value)
  } catch (error) {
    fieldErrors.value = getApiValidationErrors(error)
    formError.value = getApiErrorMessage(error, 'Nao foi possivel entrar. Verifique suas credenciais.')
  }
}
</script>

<template>
  <form class="mt-6 space-y-4" @submit.prevent="submit">
    <ErrorState v-if="formError" :message="formError" />

    <FormField label="E-mail" :error="fieldErrors.email?.[0]">
      <AppInput
        v-model="email"
        type="email"
        placeholder="owner@autoestoque.test"
        autocomplete="email"
      />
    </FormField>

    <FormField label="Senha" :error="fieldErrors.password?.[0]">
      <AppInput
        v-model="password"
        type="password"
        placeholder="password"
        autocomplete="current-password"
      />
    </FormField>

    <AppButton class="w-full" type="submit" :loading="loading">
      Entrar
    </AppButton>
  </form>
</template>

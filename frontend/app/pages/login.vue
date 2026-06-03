<script setup lang="ts">
import LoginForm from '~/modules/auth/components/LoginForm.vue'

definePageMeta({
  layout: 'public',
  middleware: 'guest',
  title: 'Login',
})
</script>

<template>
  <section class="rounded-lg border bg-background p-6 shadow-sm">
    <div class="space-y-2">
      <AppBadge>AutoEstoque</AppBadge>
      <h1 class="text-2xl font-semibold tracking-normal">
        Entrar
      </h1>
      <p class="text-sm text-muted-foreground">
        Acesse o painel administrativo da oficina.
      </p>
    </div>

    <LoginForm />
  </section>
</template>

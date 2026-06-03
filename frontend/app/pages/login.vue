<script setup lang="ts">
definePageMeta({
  layout: 'public',
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

    <form class="mt-6 space-y-4">
      <FormField label="E-mail">
        <AppInput type="email" placeholder="owner@autoestoque.test" autocomplete="email" />
      </FormField>

      <FormField label="Senha">
        <AppInput type="password" placeholder="password" autocomplete="current-password" />
      </FormField>

      <AppButton class="w-full" type="button">
        Entrar
      </AppButton>
    </form>
  </section>
</template>

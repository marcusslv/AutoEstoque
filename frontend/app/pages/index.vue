<script setup lang="ts">
const config = useRuntimeConfig()
</script>

<template>
  <main class="min-h-screen bg-background px-6 py-8">
    <section class="mx-auto flex max-w-5xl flex-col gap-6">
      <div class="space-y-2">
        <p class="text-sm font-medium text-muted-foreground">
          AutoEstoque Front-end
        </p>
        <h1 class="text-2xl font-semibold tracking-normal">
          Setup tecnico do front-end concluido
        </h1>
        <p class="max-w-2xl text-sm text-muted-foreground">
          Nuxt, Tailwind, Pinia e a estrutura Atomic Design inicial estao prontos para a proxima fase.
        </p>
      </div>

      <div class="rounded-lg border bg-card p-4 text-sm text-card-foreground">
        <span class="font-medium">API Base:</span>
        <code class="ml-2 rounded bg-muted px-2 py-1">{{ config.public.apiBaseUrl }}</code>
      </div>

      <div class="flex items-center gap-3">
        <AppButton>Botao atomico</AppButton>
        <AppBadge>Fase 0</AppBadge>
      </div>
    </section>
  </main>
</template>

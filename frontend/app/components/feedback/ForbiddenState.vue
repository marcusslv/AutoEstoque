<script setup lang="ts">
defineProps<{
  title?: string
  description?: string
}>()
</script>

<template>
  <div class="rounded-lg border bg-card p-6 text-center text-card-foreground">
    <h2 class="text-sm font-medium">
      {{ title ?? 'Acesso restrito' }}
    </h2>
    <p class="mt-1 text-sm text-muted-foreground">
      {{ description ?? 'Seu perfil nao possui permissao para acessar este recurso.' }}
    </p>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: 'authenticated',
  middleware: ['auth', 'role'],
  permission: 'settings',
  title: 'Configuracoes',
})
</script>

<template>
  <EmptyState
    title="Configuracoes"
    description="Esta tela sera implementada em uma fase futura."
  />
</template>

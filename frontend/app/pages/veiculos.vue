<script setup lang="ts">
definePageMeta({
  layout: 'authenticated',
  middleware: ['auth', 'role'],
  permission: 'workshop',
  title: 'Veiculos',
})
</script>

<template>
  <EmptyState
    title="Veiculos"
    description="Esta tela sera implementada na fase de operacao da oficina."
  />
</template>

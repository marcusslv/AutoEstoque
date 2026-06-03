<script setup lang="ts">
definePageMeta({
  layout: 'authenticated',
  middleware: ['auth', 'role'],
  permission: 'inventory',
  title: 'Estoque',
})
</script>

<template>
  <EmptyState
    title="Estoque"
    description="Esta tela sera implementada na fase de consulta de estoque."
  />
</template>

<script setup lang="ts">
definePageMeta({
  layout: 'authenticated',
  middleware: ['auth', 'role'],
  permission: 'catalog',
  title: 'Produtos',
})
</script>

<template>
  <EmptyState
    title="Produtos"
    description="Esta tela sera implementada na fase de manutencao de produtos."
  />
</template>

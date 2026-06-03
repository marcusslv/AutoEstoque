<script setup lang="ts">
definePageMeta({
  layout: 'authenticated',
  middleware: ['auth', 'role'],
  permission: 'workshop',
  title: 'Ordens de servico',
})
</script>

<template>
  <EmptyState
    title="Ordens de servico"
    description="Esta tela sera implementada na fase de ordens de servico."
  />
</template>

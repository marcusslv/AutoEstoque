<script setup lang="ts">
definePageMeta({
  layout: 'authenticated',
  middleware: ['auth', 'role'],
  permission: 'inventory',
  title: 'Alertas',
})
</script>

<template>
  <EmptyState
    title="Alertas"
    description="Esta tela sera implementada na fase de alertas operacionais."
  />
</template>

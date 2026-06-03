<script setup lang="ts">
definePageMeta({
  layout: 'authenticated',
  middleware: ['auth', 'role'],
  permission: 'manual_inventory',
  title: 'Movimentacoes',
})

await navigateTo('/inventory/movements', { replace: true })
</script>

<template>
  <LoadingState message="Abrindo movimentacoes de estoque..." />
</template>

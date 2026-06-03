<script setup lang="ts">
definePageMeta({
  layout: 'authenticated',
  middleware: ['auth', 'role'],
  permission: 'manual_inventory',
  title: 'Alertas',
})

await navigateTo('/inventory/alerts', { replace: true })
</script>

<template>
  <LoadingState message="Abrindo alertas de estoque..." />
</template>

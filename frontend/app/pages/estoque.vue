<script setup lang="ts">
definePageMeta({
  layout: 'authenticated',
  middleware: ['auth', 'role'],
  permission: 'inventory',
  title: 'Estoque',
})

await navigateTo('/stock', { replace: true })
</script>

<template>
  <LoadingState message="Abrindo consulta de estoque..." />
</template>

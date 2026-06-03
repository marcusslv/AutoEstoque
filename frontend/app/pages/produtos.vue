<script setup lang="ts">
definePageMeta({
  layout: 'authenticated',
  middleware: ['auth', 'role'],
  permission: 'catalog',
  title: 'Produtos',
})

await navigateTo('/products', { replace: true })
</script>

<template>
  <LoadingState message="Abrindo cadastro de produtos..." />
</template>

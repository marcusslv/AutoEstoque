<script setup lang="ts">
definePageMeta({
  layout: 'authenticated',
  middleware: ['auth', 'role'],
  permission: 'workshop',
  title: 'Veiculos',
})

await navigateTo('/vehicles', { replace: true })
</script>

<template>
  <LoadingState message="Abrindo cadastro de veiculos..." />
</template>

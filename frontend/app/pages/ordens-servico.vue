<script setup lang="ts">
definePageMeta({
  layout: 'authenticated',
  middleware: ['auth', 'role'],
  permission: 'workshop',
  title: 'Ordens de servico',
})

await navigateTo('/service-orders', { replace: true })
</script>

<template>
  <LoadingState message="Abrindo ordens de servico..." />
</template>

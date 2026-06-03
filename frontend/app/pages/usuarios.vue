<script setup lang="ts">
definePageMeta({
  layout: 'authenticated',
  middleware: ['auth', 'role'],
  permission: 'users',
  title: 'Usuarios',
})

await navigateTo('/users', { replace: true })
</script>

<template>
  <LoadingState message="Abrindo gestao de usuarios..." />
</template>

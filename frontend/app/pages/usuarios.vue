<script setup lang="ts">
definePageMeta({
  layout: 'authenticated',
  middleware: ['auth', 'role'],
  permission: 'users',
  title: 'Usuarios',
})
</script>

<template>
  <EmptyState
    title="Usuarios"
    description="Esta tela sera implementada na fase de gestao de usuarios."
  />
</template>

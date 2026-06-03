<script setup lang="ts">
definePageMeta({
  layout: 'authenticated',
  middleware: 'auth',
  title: 'Acesso Restrito',
})
</script>

<template>
  <ForbiddenState />
</template>

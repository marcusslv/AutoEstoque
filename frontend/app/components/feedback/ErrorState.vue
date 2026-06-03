<script setup lang="ts">
defineProps<{
  message?: string
}>()
</script>

<template>
  <div class="rounded-lg border border-destructive/30 bg-destructive/5 p-4 text-sm text-destructive">
    {{ message ?? 'Nao foi possivel carregar os dados.' }}
  </div>
</template>

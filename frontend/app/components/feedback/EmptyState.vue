<script setup lang="ts">
defineProps<{
  title?: string
  description?: string
}>()
</script>

<template>
  <div class="rounded-lg border p-6 text-center">
    <h2 class="text-sm font-medium">
      {{ title ?? 'Nenhum dado encontrado' }}
    </h2>
    <p v-if="description" class="mt-1 text-sm text-muted-foreground">
      {{ description }}
    </p>
  </div>
</template>

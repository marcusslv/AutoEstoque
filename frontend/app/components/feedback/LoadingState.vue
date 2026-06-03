<script setup lang="ts">
withDefaults(defineProps<{
  message?: string
}>(), {
  message: 'Carregando...',
})
</script>

<template>
  <div class="flex items-center gap-2 rounded-lg border p-4 text-sm text-muted-foreground">
    <AppSpinner class="h-4 w-4" />
    <span>{{ message }}</span>
  </div>
</template>

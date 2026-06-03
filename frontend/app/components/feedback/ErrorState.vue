<script setup lang="ts">
defineProps<{
  title?: string
  message?: string
  retryLabel?: string
}>()

defineEmits<{
  retry: []
}>()
</script>

<template>
  <div class="rounded-lg border border-destructive/30 bg-destructive/5 p-4 text-sm text-destructive">
    <p class="font-medium">
      {{ title ?? 'Erro ao carregar' }}
    </p>
    <p class="mt-1">
      {{ message ?? 'Nao foi possivel carregar os dados.' }}
    </p>
    <AppButton v-if="retryLabel" class="mt-3" size="sm" variant="danger" @click="$emit('retry')">
      {{ retryLabel }}
    </AppButton>
  </div>
</template>

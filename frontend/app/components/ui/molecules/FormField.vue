<script setup lang="ts">
defineProps<{
  label: string
  for?: string
  hint?: string
  error?: string
  required?: boolean
}>()
</script>

<template>
  <div class="space-y-2">
    <div class="flex items-center justify-between gap-3">
      <AppLabel :for="for">
        {{ label }}
        <span v-if="required" class="text-destructive">*</span>
      </AppLabel>
      <span v-if="$slots.aside" class="text-xs text-muted-foreground">
        <slot name="aside" />
      </span>
    </div>

    <slot />

    <p v-if="error" class="text-xs text-destructive">
      {{ error }}
    </p>
    <p v-else-if="hint" class="text-xs text-muted-foreground">
      {{ hint }}
    </p>
  </div>
</template>

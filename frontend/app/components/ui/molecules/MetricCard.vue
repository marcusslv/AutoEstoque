<script setup lang="ts">
defineProps<{
  label: string
  value: string | number
  description?: string
}>()
</script>

<template>
  <section class="rounded-lg border bg-card p-4 text-card-foreground">
    <div class="flex items-start justify-between gap-3">
      <div class="min-w-0 space-y-1">
        <p class="text-sm text-muted-foreground">
          {{ label }}
        </p>
        <p class="truncate text-2xl font-semibold tracking-normal">
          {{ value }}
        </p>
      </div>
      <div v-if="$slots.icon" class="text-muted-foreground">
        <slot name="icon" />
      </div>
    </div>
    <p v-if="description" class="mt-3 text-xs text-muted-foreground">
      {{ description }}
    </p>
  </section>
</template>

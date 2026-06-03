<script setup lang="ts">
type StatusTone = 'success' | 'warning' | 'danger' | 'neutral'

const props = withDefaults(defineProps<{
  label: string
  tone?: StatusTone
}>(), {
  tone: 'neutral',
})
</script>

<template>
  <span
    class="inline-flex items-center gap-1.5 rounded-md px-2 py-1 text-xs font-medium"
    :class="{
      'bg-emerald-50 text-emerald-700': props.tone === 'success',
      'bg-amber-50 text-amber-700': props.tone === 'warning',
      'bg-destructive/10 text-destructive': props.tone === 'danger',
      'bg-secondary text-secondary-foreground': props.tone === 'neutral',
    }"
  >
    <span
      class="h-1.5 w-1.5 rounded-full"
      :class="{
        'bg-emerald-600': props.tone === 'success',
        'bg-amber-600': props.tone === 'warning',
        'bg-destructive': props.tone === 'danger',
        'bg-muted-foreground': props.tone === 'neutral',
      }"
    />
    {{ props.label }}
  </span>
</template>

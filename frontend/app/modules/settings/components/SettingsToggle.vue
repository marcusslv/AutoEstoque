<script setup lang="ts">
const props = defineProps<{
  modelValue: boolean
  label: string
  description?: string
  disabled?: boolean
}>()

const emit = defineEmits<{
  'update:modelValue': [value: boolean]
}>()
</script>

<template>
  <label class="flex min-h-14 cursor-pointer items-start gap-3 rounded-md border bg-background p-3 transition-colors hover:bg-accent/50 has-[:disabled]:cursor-not-allowed has-[:disabled]:opacity-60">
    <input
      type="checkbox"
      class="mt-0.5 h-4 w-4 rounded border-input accent-primary"
      :checked="props.modelValue"
      :disabled="props.disabled"
      @change="emit('update:modelValue', ($event.target as HTMLInputElement).checked)"
    >
    <span class="min-w-0">
      <span class="block text-sm font-medium text-foreground">{{ label }}</span>
      <span v-if="description" class="mt-1 block text-xs leading-5 text-muted-foreground">
        {{ description }}
      </span>
    </span>
  </label>
</template>

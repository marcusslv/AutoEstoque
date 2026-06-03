<script setup lang="ts">
withDefaults(defineProps<{
  modelValue?: string | number
  placeholder?: string
  type?: string
  disabled?: boolean
  readonly?: boolean
  name?: string
  autocomplete?: string
}>(), {
  type: 'text',
  disabled: false,
  readonly: false,
})

defineEmits<{
  'update:modelValue': [value: string]
}>()
</script>

<template>
  <input
    :value="modelValue"
    :type="type"
    :name="name"
    :disabled="disabled"
    :readonly="readonly"
    :autocomplete="autocomplete"
    :placeholder="placeholder"
    class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm outline-none transition-colors placeholder:text-muted-foreground focus-visible:ring-2 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
    @input="$emit('update:modelValue', ($event.target as HTMLInputElement).value)"
  >
</template>

<script setup lang="ts">
export type AppSelectOption = {
  label: string
  value: string
  disabled?: boolean
}

withDefaults(defineProps<{
  modelValue?: string
  options: AppSelectOption[]
  placeholder?: string
  disabled?: boolean
  name?: string
}>(), {
  disabled: false,
})

defineEmits<{
  'update:modelValue': [value: string]
}>()
</script>

<template>
  <select
    :value="modelValue ?? ''"
    :disabled="disabled"
    :name="name"
    class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm outline-none transition-colors focus-visible:ring-2 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
    @change="$emit('update:modelValue', ($event.target as HTMLSelectElement).value)"
  >
    <option v-if="placeholder" value="" disabled>
      {{ placeholder }}
    </option>
    <option
      v-for="option in options"
      :key="option.value"
      :value="option.value"
      :disabled="option.disabled"
    >
      {{ option.label }}
    </option>
  </select>
</template>

<script setup lang="ts">
defineProps<{
  modelValue: string
  placeholder?: string
}>()

defineEmits<{
  'update:modelValue': [value: string]
}>()
</script>

<template>
  <AppInput
    :model-value="modelValue"
    :placeholder="placeholder ?? 'Buscar'"
    @update:model-value="$emit('update:modelValue', $event)"
  />
</template>

<script setup lang="ts">
import { Search } from 'lucide-vue-next'

defineProps<{
  modelValue: string
  placeholder?: string
}>()

defineEmits<{
  'update:modelValue': [value: string]
}>()
</script>

<template>
  <div class="relative">
    <Search class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
    <AppInput
      :model-value="modelValue"
      :placeholder="placeholder ?? 'Buscar'"
      class="pl-9"
      @update:model-value="$emit('update:modelValue', $event)"
    />
  </div>
</template>

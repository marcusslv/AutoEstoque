<script setup lang="ts">
defineProps<{
  startDate?: string
  endDate?: string
}>()

defineEmits<{
  'update:startDate': [value: string]
  'update:endDate': [value: string]
}>()
</script>

<template>
  <div class="grid gap-3 sm:grid-cols-2">
    <FormField label="Inicio">
      <AppInput
        :model-value="startDate"
        type="date"
        @update:model-value="$emit('update:startDate', $event)"
      />
    </FormField>
    <FormField label="Fim">
      <AppInput
        :model-value="endDate"
        type="date"
        @update:model-value="$emit('update:endDate', $event)"
      />
    </FormField>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  search?: string
  searchPlaceholder?: string
}>()

defineEmits<{
  'update:search': [value: string]
  reset: []
}>()
</script>

<template>
  <section class="flex flex-col gap-3 rounded-lg border bg-card p-3 sm:flex-row sm:items-center sm:justify-between">
    <div class="min-w-0 flex-1">
      <SearchInput
        :model-value="search ?? ''"
        :placeholder="searchPlaceholder"
        @update:model-value="$emit('update:search', $event)"
      />
    </div>

    <div class="flex flex-wrap items-center gap-2">
      <slot />
      <AppButton variant="ghost" size="sm" @click="$emit('reset')">
        Limpar
      </AppButton>
    </div>
  </section>
</template>

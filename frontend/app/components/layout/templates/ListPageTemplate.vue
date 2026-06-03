<script setup lang="ts">
defineProps<{
  title: string
  description?: string
}>()
</script>

<template>
  <section class="space-y-4">
    <PageHeader :title="title" :description="description">
      <template #actions>
        <slot name="actions" />
      </template>
    </PageHeader>

    <slot name="filters" />
    <slot />
  </section>
</template>

<script setup lang="ts">
defineProps<{
  title: string
  description?: string
}>()
</script>

<template>
  <section class="space-y-5">
    <PageHeader :title="title" :description="description">
      <template #actions>
        <slot name="actions" />
      </template>
    </PageHeader>

    <div v-if="$slots.metrics" class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
      <slot name="metrics" />
    </div>

    <div class="grid gap-5 xl:grid-cols-[minmax(0,1fr)_22rem]">
      <main class="min-w-0 space-y-5">
        <slot />
      </main>
      <aside v-if="$slots.aside" class="space-y-5">
        <slot name="aside" />
      </aside>
    </div>
  </section>
</template>

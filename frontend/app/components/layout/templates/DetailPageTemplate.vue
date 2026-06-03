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

    <div class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_18rem]">
      <main class="min-w-0 space-y-5">
        <slot />
      </main>
      <aside v-if="$slots.aside" class="space-y-4">
        <slot name="aside" />
      </aside>
    </div>
  </section>
</template>

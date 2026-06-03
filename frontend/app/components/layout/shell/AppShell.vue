<script setup lang="ts">
const sidebarOpen = ref(false)

const route = useRoute()

watch(() => route.fullPath, () => {
  sidebarOpen.value = false
})
</script>

<template>
  <div class="min-h-screen bg-background">
    <AppSidebar v-model:open="sidebarOpen" />

    <div class="min-h-screen lg:pl-64">
      <AppHeader :title="String(route.meta.title ?? 'Painel')" @toggle-sidebar="sidebarOpen = true" />

      <main class="px-4 py-6 lg:px-6">
        <slot />
      </main>
    </div>
  </div>
</template>

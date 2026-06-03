<script setup lang="ts">
import { Menu, Search } from 'lucide-vue-next'

defineProps<{
  title?: string
}>()

defineEmits<{
  toggleSidebar: []
}>()
</script>

<template>
  <header class="sticky top-0 z-30 border-b bg-background/95 backdrop-blur">
    <div class="flex h-14 items-center gap-3 px-4 lg:px-6">
      <AppIconButton class="lg:hidden" label="Abrir menu" @click="$emit('toggleSidebar')">
        <Menu class="h-4 w-4" />
      </AppIconButton>

      <div class="min-w-0 flex-1">
        <p class="truncate text-sm font-medium">
          {{ title ?? 'Painel' }}
        </p>
      </div>

      <div class="hidden w-full max-w-xs md:block">
        <div class="relative">
          <Search class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
          <AppInput placeholder="Buscar no sistema" class="pl-9" />
        </div>
      </div>

      <UserMenu />
    </div>
  </header>
</template>

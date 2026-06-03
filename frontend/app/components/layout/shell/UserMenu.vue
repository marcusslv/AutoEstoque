<script setup lang="ts">
import { LogOut, User } from 'lucide-vue-next'

const open = ref(false)
</script>

<template>
  <div class="relative">
    <AppIconButton label="Menu do usuario" variant="secondary" @click="open = !open">
      <User class="h-4 w-4" />
    </AppIconButton>

    <div
      v-if="open"
      class="absolute right-0 mt-2 w-56 rounded-lg border bg-card p-2 text-card-foreground shadow-md"
    >
      <div class="px-2 py-2">
        <p class="text-sm font-medium">
          Usuario Demo
        </p>
        <p class="text-xs text-muted-foreground">
          owner@autoestoque.test
        </p>
      </div>

      <AppSeparator />

      <button
        type="button"
        class="mt-2 flex h-9 w-full items-center gap-2 rounded-md px-2 text-sm text-muted-foreground transition-colors hover:bg-accent hover:text-accent-foreground"
      >
        <LogOut class="h-4 w-4" />
        Sair
      </button>
    </div>
  </div>
</template>

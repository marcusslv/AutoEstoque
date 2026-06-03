<script setup lang="ts">
import { LogOut, User } from 'lucide-vue-next'
import { useAuth } from '~/modules/auth/composables/useAuth'

const open = ref(false)
const { user, logout, loading } = useAuth()
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
          {{ user?.name ?? 'Usuario' }}
        </p>
        <p class="text-xs text-muted-foreground">
          {{ user?.email ?? 'Sessao ativa' }}
        </p>
      </div>

      <AppSeparator />

      <button
        type="button"
        :disabled="loading"
        class="mt-2 flex h-9 w-full items-center gap-2 rounded-md px-2 text-sm text-muted-foreground transition-colors hover:bg-accent hover:text-accent-foreground"
        @click="logout"
      >
        <LogOut class="h-4 w-4" />
        {{ loading ? 'Saindo...' : 'Sair' }}
      </button>
    </div>
  </div>
</template>

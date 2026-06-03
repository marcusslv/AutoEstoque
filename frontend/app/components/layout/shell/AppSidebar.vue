<script setup lang="ts">
import {
  Bell,
  Boxes,
  Car,
  ClipboardList,
  LayoutDashboard,
  Package,
  Settings,
  Users,
  X,
} from 'lucide-vue-next'
import type { Component } from 'vue'
import { cn } from '~/shared/utils/cn'

type NavigationItem = {
  label: string
  to: string
  icon: Component
}

const props = defineProps<{
  open: boolean
}>()

const emit = defineEmits<{
  'update:open': [value: boolean]
}>()

const route = useRoute()

const navigationItems: NavigationItem[] = [
  { label: 'Dashboard', to: '/dashboard', icon: LayoutDashboard },
  { label: 'Produtos', to: '/produtos', icon: Package },
  { label: 'Estoque', to: '/estoque', icon: Boxes },
  { label: 'Alertas', to: '/alertas', icon: Bell },
  { label: 'Veiculos', to: '/veiculos', icon: Car },
  { label: 'Ordens de servico', to: '/ordens-servico', icon: ClipboardList },
  { label: 'Usuarios', to: '/usuarios', icon: Users },
  { label: 'Configuracoes', to: '/configuracoes', icon: Settings },
]

const isActive = (to: string) => route.path === to || route.path.startsWith(`${to}/`)
</script>

<template>
  <Teleport to="body">
    <div
      v-if="props.open"
      class="fixed inset-0 z-40 bg-foreground/40 lg:hidden"
      @click="emit('update:open', false)"
    />
  </Teleport>

  <aside
    :class="cn(
      'fixed inset-y-0 left-0 z-50 flex w-64 flex-col border-r bg-card text-card-foreground transition-transform lg:translate-x-0',
      props.open ? 'translate-x-0' : '-translate-x-full',
    )"
  >
    <div class="flex h-14 items-center justify-between border-b px-4">
      <NuxtLink to="/dashboard" class="flex items-center gap-2">
        <span class="flex h-8 w-8 items-center justify-center rounded-md bg-primary text-sm font-semibold text-primary-foreground">
          AE
        </span>
        <span class="text-sm font-semibold">AutoEstoque</span>
      </NuxtLink>

      <AppIconButton class="lg:hidden" label="Fechar menu" @click="emit('update:open', false)">
        <X class="h-4 w-4" />
      </AppIconButton>
    </div>

    <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
      <NuxtLink
        v-for="item in navigationItems"
        :key="item.to"
        :to="item.to"
        :class="cn(
          'flex h-9 items-center gap-3 rounded-md px-3 text-sm font-medium transition-colors',
          isActive(item.to)
            ? 'bg-primary text-primary-foreground'
            : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground',
        )"
      >
        <component :is="item.icon" class="h-4 w-4 shrink-0" />
        <span class="truncate">{{ item.label }}</span>
      </NuxtLink>
    </nav>

    <div class="border-t px-4 py-3 text-xs text-muted-foreground">
      Oficina Demo
    </div>
  </aside>
</template>

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
import type { PermissionKey } from '~/shared/permissions/permissions'
import { usePermissions } from '~/shared/permissions/usePermissions'

type NavigationItem = {
  label: string
  to: string
  icon: Component
  permission: PermissionKey
  enabled?: boolean
}

const props = defineProps<{
  open: boolean
}>()

const emit = defineEmits<{
  'update:open': [value: boolean]
}>()

const route = useRoute()
const { canAccess } = usePermissions()

const navigationItems: NavigationItem[] = [
  { label: 'Dashboard', to: '/dashboard', icon: LayoutDashboard, permission: 'dashboard', enabled: true },
  { label: 'Produtos', to: '/products', icon: Package, permission: 'catalog', enabled: true },
  { label: 'Estoque', to: '/stock', icon: Boxes, permission: 'inventory', enabled: true },
  { label: 'Alertas', to: '/alertas', icon: Bell, permission: 'inventory', enabled: false },
  { label: 'Veiculos', to: '/veiculos', icon: Car, permission: 'workshop', enabled: false },
  { label: 'Ordens de servico', to: '/ordens-servico', icon: ClipboardList, permission: 'workshop', enabled: false },
  { label: 'Usuarios', to: '/usuarios', icon: Users, permission: 'users', enabled: false },
  { label: 'Configuracoes', to: '/configuracoes', icon: Settings, permission: 'settings', enabled: false },
]

const visibleNavigationItems = computed(() => {
  return navigationItems.filter((item) => canAccess(item.permission))
})

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
      <template v-for="item in visibleNavigationItems" :key="item.to">
        <NuxtLink
          v-if="item.enabled"
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

        <button
          v-else
          type="button"
          disabled
          class="flex h-9 w-full items-center gap-3 rounded-md px-3 text-left text-sm font-medium text-muted-foreground/60"
        >
          <component :is="item.icon" class="h-4 w-4 shrink-0" />
          <span class="truncate">{{ item.label }}</span>
        </button>
      </template>
    </nav>

    <div class="border-t px-4 py-3 text-xs text-muted-foreground">
      Oficina Demo
    </div>
  </aside>
</template>

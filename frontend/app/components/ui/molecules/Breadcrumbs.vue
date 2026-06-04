<script setup lang="ts">
const route = useRoute()

const labels: Record<string, string> = {
  dashboard: 'Dashboard',
  products: 'Produtos',
  stock: 'Estoque',
  inventory: 'Estoque',
  movements: 'Movimentacoes',
  alerts: 'Alertas',
  vehicles: 'Veiculos',
  'service-orders': 'Ordens de servico',
  users: 'Usuarios',
  configuracoes: 'Configuracoes',
}

const breadcrumbs = computed(() => {
  const segments = route.path.split('/').filter(Boolean)

  return segments.map((segment, index) => {
    const rawTo = `/${segments.slice(0, index + 1).join('/')}`
    const to = segment === 'inventory' ? '/stock' : rawTo
    const isUuidLike = /^[0-9a-f-]{8,}$/i.test(segment)

    return {
      label: isUuidLike ? 'Detalhe' : labels[segment] ?? segment,
      to,
      current: index === segments.length - 1,
    }
  })
})
</script>

<template>
  <nav v-if="route.path !== '/dashboard' && breadcrumbs.length" aria-label="Breadcrumb" class="text-xs text-muted-foreground">
    <ol class="flex flex-wrap items-center gap-1">
      <li>
        <NuxtLink to="/dashboard" class="hover:text-foreground">
          Inicio
        </NuxtLink>
      </li>
      <li v-for="item in breadcrumbs" :key="item.to" class="flex items-center gap-1">
        <span>/</span>
        <span v-if="item.current" class="text-foreground">
          {{ item.label }}
        </span>
        <NuxtLink v-else :to="item.to" class="hover:text-foreground">
          {{ item.label }}
        </NuxtLink>
      </li>
    </ol>
  </nav>
</template>

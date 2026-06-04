<script setup lang="ts">
import type { MostConsumedProduct } from '../types/dashboard'

const props = defineProps<{
  products: MostConsumedProduct[]
}>()
</script>

<template>
  <section class="space-y-3 rounded-lg border bg-card p-4">
    <div>
      <h2 class="text-sm font-medium">
        Mais consumidos
      </h2>
      <p class="mt-1 text-xs text-muted-foreground">
        Ranking por saidas no periodo atual.
      </p>
    </div>

    <div v-if="props.products.length" class="space-y-3">
      <article
        v-for="(item, index) in props.products"
        :key="item.productId"
        class="flex items-center gap-3 rounded-md border bg-background p-3"
      >
        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-primary/10 text-xs font-semibold text-primary">
          {{ index + 1 }}
        </span>

        <div class="min-w-0 flex-1">
          <p class="truncate text-sm font-medium">
            {{ item.product.name }}
          </p>
          <p class="truncate text-xs text-muted-foreground">
            {{ item.product.sku }} - {{ item.movementsCount }} movimentacoes
          </p>
        </div>

        <div class="text-right">
          <p class="text-sm font-semibold">
            {{ item.totalQuantity }}
          </p>
          <p class="text-xs text-muted-foreground">
            un.
          </p>
        </div>
      </article>
    </div>

    <EmptyState
      v-else
      title="Sem consumo registrado"
      description="Produtos consumidos em servico aparecem neste ranking."
    />
  </section>
</template>

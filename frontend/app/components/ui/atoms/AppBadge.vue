<script setup lang="ts">
import { cn } from '~/shared/utils/cn'

type BadgeVariant = 'default' | 'secondary' | 'danger'

const props = withDefaults(defineProps<{
  variant?: BadgeVariant
}>(), {
  variant: 'default',
})
</script>

<template>
  <span
    :class="cn(
      'inline-flex items-center rounded-md px-2 py-1 text-xs font-medium',
      props.variant === 'default' && 'bg-primary/10 text-primary',
      props.variant === 'secondary' && 'bg-secondary text-secondary-foreground',
      props.variant === 'danger' && 'bg-destructive/10 text-destructive',
    )"
  >
    <slot />
  </span>
</template>

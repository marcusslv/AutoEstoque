<script setup lang="ts">
import { cn } from '~/shared/utils/cn'

type BadgeVariant = 'default' | 'secondary' | 'danger'
type BadgeTone = 'solid' | 'soft'

const props = withDefaults(defineProps<{
  variant?: BadgeVariant
  tone?: BadgeTone
}>(), {
  variant: 'default',
  tone: 'soft',
})
</script>

<template>
  <span
    :class="cn(
      'inline-flex items-center rounded-md px-2 py-1 text-xs font-medium',
      props.variant === 'default' && props.tone === 'soft' && 'bg-primary/10 text-primary',
      props.variant === 'default' && props.tone === 'solid' && 'bg-primary text-primary-foreground',
      props.variant === 'secondary' && props.tone === 'soft' && 'bg-secondary text-secondary-foreground',
      props.variant === 'secondary' && props.tone === 'solid' && 'bg-foreground text-background',
      props.variant === 'danger' && props.tone === 'soft' && 'bg-destructive/10 text-destructive',
      props.variant === 'danger' && props.tone === 'solid' && 'bg-destructive text-destructive-foreground',
    )"
  >
    <slot />
  </span>
</template>

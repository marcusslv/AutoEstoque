<script setup lang="ts">
import { cn } from '~/shared/utils/cn'

type IconButtonVariant = 'primary' | 'secondary' | 'ghost' | 'danger'

const props = withDefaults(defineProps<{
  label: string
  variant?: IconButtonVariant
  disabled?: boolean
  type?: 'button' | 'submit' | 'reset'
}>(), {
  variant: 'ghost',
  disabled: false,
  type: 'button',
})
</script>

<template>
  <button
    :type="props.type"
    :aria-label="props.label"
    :title="props.label"
    :disabled="props.disabled"
    :class="cn(
      'inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-md transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50',
      props.variant === 'primary' && 'bg-primary text-primary-foreground hover:bg-primary/90',
      props.variant === 'secondary' && 'bg-secondary text-secondary-foreground hover:bg-secondary/80',
      props.variant === 'ghost' && 'hover:bg-accent hover:text-accent-foreground',
      props.variant === 'danger' && 'bg-destructive text-destructive-foreground hover:bg-destructive/90',
    )"
  >
    <slot />
  </button>
</template>

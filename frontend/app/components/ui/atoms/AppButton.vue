<script setup lang="ts">
import { cn } from '~/shared/utils/cn'

type ButtonVariant = 'primary' | 'secondary' | 'danger' | 'ghost'
type ButtonSize = 'sm' | 'md' | 'lg'

const props = withDefaults(defineProps<{
  variant?: ButtonVariant
  size?: ButtonSize
  loading?: boolean
  disabled?: boolean
  type?: 'button' | 'submit' | 'reset'
}>(), {
  variant: 'primary',
  size: 'md',
  loading: false,
  disabled: false,
  type: 'button',
})
</script>

<template>
  <button
    :type="props.type"
    :disabled="props.disabled || props.loading"
    :class="cn(
      'inline-flex items-center justify-center gap-2 rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50',
      props.size === 'sm' && 'h-8 px-2.5',
      props.size === 'md' && 'h-9 px-3',
      props.size === 'lg' && 'h-10 px-4',
      props.variant === 'primary' && 'bg-primary text-primary-foreground hover:bg-primary/90',
      props.variant === 'secondary' && 'bg-secondary text-secondary-foreground hover:bg-secondary/80',
      props.variant === 'danger' && 'bg-destructive text-destructive-foreground hover:bg-destructive/90',
      props.variant === 'ghost' && 'hover:bg-accent hover:text-accent-foreground',
    )"
  >
    <AppSpinner v-if="props.loading" class="h-4 w-4" />
    <slot />
  </button>
</template>

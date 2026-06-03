<script setup lang="ts">
import { cn } from '~/shared/utils/cn'

type ButtonVariant = 'primary' | 'secondary' | 'danger' | 'ghost'

const props = withDefaults(defineProps<{
  variant?: ButtonVariant
  loading?: boolean
  disabled?: boolean
  type?: 'button' | 'submit' | 'reset'
}>(), {
  variant: 'primary',
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
      'inline-flex h-9 items-center justify-center gap-2 rounded-md px-3 text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50',
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

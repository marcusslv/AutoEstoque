<script setup lang="ts">
import type { UserStatus } from '../types/user'

const props = defineProps<{
  status: UserStatus
}>()

const badge = computed(() => {
  if (props.status === 'active') {
    return {
      label: 'Ativo',
      tone: 'success' as const,
    }
  }

  return {
    label: 'Inativo',
    tone: 'neutral' as const,
  }
})
</script>

<template>
  <StatusBadge :label="badge.label" :tone="badge.tone" />
</template>

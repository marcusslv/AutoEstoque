<script setup lang="ts">
import type { Role } from '~/shared/permissions/roles'

const props = defineProps<{
  role: Role
}>()

const roleLabels: Record<Role, string> = {
  owner: 'Proprietario',
  manager: 'Gerente',
  admin: 'Administrador',
  mechanic: 'Mecanico',
}
</script>

<template>
  <AppBadge variant="secondary">
    {{ roleLabels[props.role] }}
  </AppBadge>
</template>

<script setup lang="ts">
import type { PermissionKey } from '~/shared/permissions/permissions'
import { usePermissions } from '~/shared/permissions/usePermissions'

const props = defineProps<{
  permission: PermissionKey
}>()

const { canAccess } = usePermissions()
</script>

<template>
  <slot v-if="canAccess(props.permission)" />
</template>

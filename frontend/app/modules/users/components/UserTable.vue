<script setup lang="ts">
import type { DataTableColumn } from '~/components/ui/organisms/DataTable.vue'
import RoleBadge from './RoleBadge.vue'
import UserStatusBadge from './UserStatusBadge.vue'
import type { UserStatus, WorkshopUser } from '../types/user'
import type { Role } from '~/shared/permissions/roles'

const props = defineProps<{
  users: WorkshopUser[]
}>()

defineEmits<{
  edit: [user: WorkshopUser]
  deactivate: [user: WorkshopUser]
}>()

const columns: DataTableColumn[] = [
  { key: 'user', label: 'Usuario' },
  { key: 'email', label: 'E-mail' },
  { key: 'role', label: 'Perfil' },
  { key: 'status', label: 'Status' },
  { key: 'actions', label: '', align: 'right' },
]

const rows = computed(() => {
  return props.users.map((user) => ({
    user: user.name,
    email: user.email,
    role: user.role,
    status: user.status,
    original: user,
  }))
})
</script>

<template>
  <DataTable
    :columns="columns"
    :rows="rows"
    empty-title="Nenhum usuario encontrado"
    empty-description="Cadastre usuarios para liberar acesso ao painel."
  >
    <template #cell-user="{ value }">
      <p class="truncate font-medium">
        {{ value }}
      </p>
    </template>

    <template #cell-role="{ value }">
      <RoleBadge :role="value as Role" />
    </template>

    <template #cell-status="{ value }">
      <UserStatusBadge :status="value as UserStatus" />
    </template>

    <template #cell-actions="{ row }">
      <div class="flex justify-end gap-2">
        <AppButton
          size="sm"
          variant="secondary"
          @click="$emit('edit', row.original as WorkshopUser)"
        >
          Editar
        </AppButton>

        <AppButton
          v-if="(row.original as WorkshopUser).status === 'active'"
          size="sm"
          variant="danger"
          @click="$emit('deactivate', row.original as WorkshopUser)"
        >
          Inativar
        </AppButton>
      </div>
    </template>
  </DataTable>
</template>

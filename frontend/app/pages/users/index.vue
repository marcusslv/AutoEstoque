<script setup lang="ts">
import { Plus, RefreshCw } from 'lucide-vue-next'
import UserDialog from '~/modules/users/components/UserDialog.vue'
import UserTable from '~/modules/users/components/UserTable.vue'
import { useUsers } from '~/modules/users/composables/useUsers'
import type { UserFormValues, WorkshopUser } from '~/modules/users/types/user'
import { getApiErrorMessage } from '~/shared/api/apiErrors'
import { useToast } from '~/shared/feedback/useToast'

definePageMeta({
  layout: 'authenticated',
  middleware: ['auth', 'role'],
  permission: 'users',
  title: 'Usuarios',
})

const dialogOpen = ref(false)
const toast = useToast()
const selectedUser = ref<WorkshopUser | null>(null)
const userToDeactivate = ref<WorkshopUser | null>(null)
const saveErrorMessage = ref<string | null>(null)
const deactivateErrorMessage = ref<string | null>(null)

const {
  users,
  total,
  loading,
  saving,
  errorMessage,
  validationErrors,
  isEmpty,
  load,
  create,
  update,
  deactivate,
} = useUsers()

const openCreateDialog = () => {
  selectedUser.value = null
  saveErrorMessage.value = null
  validationErrors.value = {}
  dialogOpen.value = true
}

const openEditDialog = (user: WorkshopUser) => {
  selectedUser.value = user
  saveErrorMessage.value = null
  validationErrors.value = {}
  dialogOpen.value = true
}

const closeDialog = () => {
  dialogOpen.value = false
  selectedUser.value = null
  saveErrorMessage.value = null
}

const saveUser = async (values: UserFormValues) => {
  saveErrorMessage.value = null

  try {
    if (selectedUser.value) {
      await update(selectedUser.value.id, values)
      toast.success('Usuario atualizado')
    } else {
      await create(values)
      toast.success('Usuario cadastrado')
    }

    closeDialog()
    await load()
  } catch (error) {
    saveErrorMessage.value = getApiErrorMessage(error, 'Nao foi possivel salvar o usuario.')
  }
}

const askDeactivate = (user: WorkshopUser) => {
  userToDeactivate.value = user
  deactivateErrorMessage.value = null
}

const cancelDeactivate = () => {
  userToDeactivate.value = null
  deactivateErrorMessage.value = null
}

const confirmDeactivate = async () => {
  if (!userToDeactivate.value) {
    return
  }

  deactivateErrorMessage.value = null

  try {
    await deactivate(userToDeactivate.value.id)
    toast.success('Usuario inativado')
    cancelDeactivate()
    await load()
  } catch (error) {
    deactivateErrorMessage.value = getApiErrorMessage(error, 'Nao foi possivel inativar o usuario.')
  }
}

await load()
</script>

<template>
  <ListPageTemplate
    title="Usuarios"
    description="Gerencie acessos, perfis e status dos usuarios da oficina."
  >
    <template #actions>
      <div class="flex items-center gap-2">
        <AppButton
          variant="secondary"
          :loading="loading"
          @click="load"
        >
          <RefreshCw class="h-4 w-4" />
          Atualizar
        </AppButton>

        <AppButton @click="openCreateDialog">
          <Plus class="h-4 w-4" />
          Novo usuario
        </AppButton>
      </div>
    </template>

    <template #filters>
      <div class="flex flex-col gap-3 rounded-lg border bg-card p-4 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-sm text-muted-foreground">
          Controle de acesso por perfil da oficina
        </p>

        <p class="text-sm text-muted-foreground">
          {{ total }} usuario{{ total === 1 ? '' : 's' }}
        </p>
      </div>
    </template>

    <LoadingState
      v-if="loading && !users.length"
      message="Carregando usuarios..."
    />

    <ErrorState
      v-else-if="errorMessage"
      title="Erro ao carregar usuarios"
      :message="errorMessage"
      retry-label="Tentar novamente"
      @retry="load"
    />

    <EmptyState
      v-else-if="isEmpty"
      title="Nenhum usuario cadastrado"
      description="Cadastre usuarios para liberar acesso ao painel da oficina."
      action-label="Novo usuario"
      @action="openCreateDialog"
    />

    <UserTable
      v-else
      :users="users"
      @edit="openEditDialog"
      @deactivate="askDeactivate"
    />

    <ErrorState
      v-if="saveErrorMessage && dialogOpen"
      class="fixed bottom-4 right-4 z-[60] max-w-sm"
      title="Erro ao salvar usuario"
      :message="saveErrorMessage"
    />

    <ErrorState
      v-if="deactivateErrorMessage && userToDeactivate"
      class="fixed bottom-4 right-4 z-[60] max-w-sm"
      title="Erro ao inativar usuario"
      :message="deactivateErrorMessage"
    />

    <UserDialog
      :open="dialogOpen"
      :user="selectedUser"
      :submitting="saving"
      :errors="validationErrors"
      @close="closeDialog"
      @submit="saveUser"
    />

    <ConfirmDialog
      :open="Boolean(userToDeactivate)"
      title="Inativar usuario"
      :description="userToDeactivate ? `O usuario ${userToDeactivate.name} perdera acesso ao sistema.` : undefined"
      confirm-label="Inativar"
      danger
      :loading="saving"
      @cancel="cancelDeactivate"
      @confirm="confirmDeactivate"
    />
  </ListPageTemplate>
</template>

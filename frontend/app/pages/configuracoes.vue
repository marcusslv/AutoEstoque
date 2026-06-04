<script setup lang="ts">
import { RefreshCw, RotateCcw, Save } from 'lucide-vue-next'
import WorkshopSettingsForm from '~/modules/settings/components/WorkshopSettingsForm.vue'
import { useWorkshopSettings } from '~/modules/settings/composables/useWorkshopSettings'
import { getApiErrorMessage } from '~/shared/api/apiErrors'
import { useToast } from '~/shared/feedback/useToast'
import { formatDateTime } from '~/shared/utils/format'

definePageMeta({
  layout: 'authenticated',
  middleware: ['auth', 'role'],
  permission: 'settings',
  title: 'Configuracoes',
})

const toast = useToast()

const {
  settings,
  form,
  loading,
  saving,
  errorMessage,
  saveErrorMessage,
  validationErrors,
  load,
  save,
  reset,
} = useWorkshopSettings()

const saveSettings = async () => {
  try {
    await save()
    toast.success('Configuracoes atualizadas')
  } catch (error) {
    toast.danger('Erro ao salvar configuracoes', getApiErrorMessage(error))
  }
}

await load()
</script>

<template>
  <DashboardPageTemplate
    title="Configuracoes"
    description="Dados da oficina, parametros operacionais e preferencias de notificacao."
  >
    <template #actions>
      <div class="flex flex-wrap items-center gap-2">
        <AppButton
          variant="secondary"
          :loading="loading"
          @click="load"
        >
          <RefreshCw class="h-4 w-4" />
          Atualizar
        </AppButton>

        <AppButton
          variant="secondary"
          :disabled="saving || loading"
          @click="reset"
        >
          <RotateCcw class="h-4 w-4" />
          Restaurar
        </AppButton>

        <AppButton
          :loading="saving"
          :disabled="loading"
          @click="saveSettings"
        >
          <Save class="h-4 w-4" />
          Salvar
        </AppButton>
      </div>
    </template>

    <LoadingState
      v-if="loading && !settings"
      message="Carregando configuracoes..."
    />

    <ErrorState
      v-else-if="errorMessage"
      title="Erro ao carregar configuracoes"
      :message="errorMessage"
      retry-label="Tentar novamente"
      @retry="load"
    />

    <WorkshopSettingsForm
      v-else
      v-model="form"
      :disabled="saving"
      :errors="validationErrors"
    />

    <ErrorState
      v-if="saveErrorMessage"
      class="fixed bottom-4 right-4 z-[60] max-w-sm"
      title="Erro ao salvar configuracoes"
      :message="saveErrorMessage"
    />

    <template v-if="settings" #aside>
      <section class="space-y-4 rounded-lg border bg-card p-4">
        <div>
          <h2 class="text-sm font-medium">
            Plano atual
          </h2>
          <p class="mt-1 text-xs text-muted-foreground">
            Recursos e limites vinculados a oficina.
          </p>
        </div>

        <div class="space-y-3">
          <div class="flex items-center justify-between gap-3">
            <span class="text-sm text-muted-foreground">Plano</span>
            <StatusBadge
              :label="settings.plan === 'pro' ? 'Pro' : 'Starter'"
              :tone="settings.plan === 'pro' ? 'success' : 'neutral'"
            />
          </div>

          <div class="flex items-center justify-between gap-3">
            <span class="text-sm text-muted-foreground">Limite de usuarios</span>
            <span class="text-sm font-medium">{{ settings.userLimit }}</span>
          </div>

          <div class="flex items-start justify-between gap-3">
            <span class="text-sm text-muted-foreground">Ultima atualizacao</span>
            <span class="max-w-36 text-right text-sm font-medium">
              {{ settings.updatedAt ? formatDateTime(settings.updatedAt) : '-' }}
            </span>
          </div>
        </div>
      </section>
    </template>
  </DashboardPageTemplate>
</template>


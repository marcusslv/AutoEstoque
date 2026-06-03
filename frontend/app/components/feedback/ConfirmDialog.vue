<script setup lang="ts">
defineProps<{
  open: boolean
  title: string
  description?: string
  confirmLabel?: string
  cancelLabel?: string
  danger?: boolean
  loading?: boolean
}>()

defineEmits<{
  cancel: []
  confirm: []
}>()
</script>

<template>
  <Teleport to="body">
    <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center bg-foreground/40 px-4 py-6">
      <section class="w-full max-w-md rounded-lg border bg-background p-5 shadow-lg">
        <h2 class="text-lg font-semibold tracking-normal">
          {{ title }}
        </h2>
        <p v-if="description" class="mt-2 text-sm text-muted-foreground">
          {{ description }}
        </p>

        <footer class="mt-5 flex justify-end gap-2">
          <AppButton type="button" variant="ghost" @click="$emit('cancel')">
            {{ cancelLabel ?? 'Cancelar' }}
          </AppButton>
          <AppButton
            type="button"
            :variant="danger ? 'danger' : 'primary'"
            :loading="loading"
            @click="$emit('confirm')"
          >
            {{ confirmLabel ?? 'Confirmar' }}
          </AppButton>
        </footer>
      </section>
    </div>
  </Teleport>
</template>

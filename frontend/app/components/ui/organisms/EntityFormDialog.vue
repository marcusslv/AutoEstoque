<script setup lang="ts">
defineProps<{
  open: boolean
  title: string
  description?: string
  submitLabel?: string
  cancelLabel?: string
  submitting?: boolean
}>()

defineEmits<{
  close: []
  submit: []
}>()
</script>

<template>
  <Teleport to="body">
    <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center bg-foreground/40 px-4 py-6">
      <section class="w-full max-w-lg rounded-lg border bg-background p-5 shadow-lg">
        <header class="space-y-1">
          <h2 class="text-lg font-semibold tracking-normal">
            {{ title }}
          </h2>
          <p v-if="description" class="text-sm text-muted-foreground">
            {{ description }}
          </p>
        </header>

        <form class="mt-5 space-y-4" @submit.prevent="$emit('submit')">
          <slot />

          <footer class="flex justify-end gap-2 pt-2">
            <AppButton type="button" variant="ghost" @click="$emit('close')">
              {{ cancelLabel ?? 'Cancelar' }}
            </AppButton>
            <AppButton type="submit" :loading="submitting">
              {{ submitLabel ?? 'Salvar' }}
            </AppButton>
          </footer>
        </form>
      </section>
    </div>
  </Teleport>
</template>

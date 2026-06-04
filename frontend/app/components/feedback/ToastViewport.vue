<script setup lang="ts">
import { CheckCircle, Info, X, XCircle } from 'lucide-vue-next'
import { useToast, type ToastMessage } from '~/shared/feedback/useToast'
import { cn } from '~/shared/utils/cn'

const { toasts, removeToast } = useToast()

const iconFor = (toast: ToastMessage) => {
  if (toast.variant === 'success') return CheckCircle
  if (toast.variant === 'danger') return XCircle

  return Info
}
</script>

<template>
  <Teleport to="body">
    <div class="fixed right-4 top-4 z-[80] flex w-[calc(100vw-2rem)] max-w-sm flex-col gap-2">
      <TransitionGroup name="toast">
        <section
          v-for="toast in toasts"
          :key="toast.id"
          :class="cn(
            'flex gap-3 rounded-lg border bg-background p-4 text-sm shadow-lg',
            toast.variant === 'success' && 'border-emerald-200',
            toast.variant === 'danger' && 'border-destructive/30',
          )"
          role="status"
        >
          <component
            :is="iconFor(toast)"
            :class="cn(
              'mt-0.5 h-4 w-4 shrink-0',
              toast.variant === 'success' && 'text-emerald-600',
              toast.variant === 'danger' && 'text-destructive',
              toast.variant === 'neutral' && 'text-muted-foreground',
            )"
          />

          <div class="min-w-0 flex-1">
            <p class="font-medium">
              {{ toast.title }}
            </p>
            <p v-if="toast.description" class="mt-1 text-muted-foreground">
              {{ toast.description }}
            </p>
          </div>

          <button
            type="button"
            class="rounded-md p-1 text-muted-foreground transition-colors hover:bg-accent hover:text-accent-foreground"
            @click="removeToast(toast.id)"
          >
            <span class="sr-only">Fechar</span>
            <X class="h-4 w-4" />
          </button>
        </section>
      </TransitionGroup>
    </div>
  </Teleport>
</template>

<style scoped>
.toast-enter-active,
.toast-leave-active {
  transition: all 160ms ease;
}

.toast-enter-from,
.toast-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}
</style>

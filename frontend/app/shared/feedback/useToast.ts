export type ToastVariant = 'success' | 'danger' | 'neutral'

export type ToastMessage = {
  id: number
  title: string
  description?: string
  variant: ToastVariant
}

export const useToast = () => {
  const toasts = useState<ToastMessage[]>('feedback:toasts', () => [])

  const removeToast = (id: number) => {
    toasts.value = toasts.value.filter((toast) => toast.id !== id)
  }

  const showToast = (toast: Omit<ToastMessage, 'id'>) => {
    const id = Date.now() + Math.floor(Math.random() * 1000)

    toasts.value = [
      ...toasts.value,
      {
        id,
        ...toast,
      },
    ]

    if (import.meta.client) {
      window.setTimeout(() => removeToast(id), 4500)
    }
  }

  const success = (title: string, description?: string) => {
    showToast({ title, description, variant: 'success' })
  }

  const danger = (title: string, description?: string) => {
    showToast({ title, description, variant: 'danger' })
  }

  const neutral = (title: string, description?: string) => {
    showToast({ title, description, variant: 'neutral' })
  }

  return {
    toasts,
    showToast,
    removeToast,
    success,
    danger,
    neutral,
  }
}

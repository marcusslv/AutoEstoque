<script setup lang="ts">
const props = withDefaults(defineProps<{
  value: number
  currency?: string
  locale?: string
}>(), {
  currency: 'BRL',
  locale: 'pt-BR',
})

const formattedValue = computed(() => new Intl.NumberFormat(props.locale, {
  style: 'currency',
  currency: props.currency,
}).format(props.value))
</script>

<template>
  <span class="tabular-nums">{{ formattedValue }}</span>
</template>

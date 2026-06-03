<script setup lang="ts">
export type DataTableColumn = {
  key: string
  label: string
  align?: 'left' | 'center' | 'right'
}

defineProps<{
  columns: DataTableColumn[]
  rows: Record<string, unknown>[]
  emptyTitle?: string
  emptyDescription?: string
}>()
</script>

<template>
  <div class="overflow-hidden rounded-lg border bg-card">
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-border text-sm">
        <thead class="bg-muted/50">
          <tr>
            <th
              v-for="column in columns"
              :key="column.key"
              scope="col"
              class="h-10 whitespace-nowrap px-4 text-xs font-medium uppercase text-muted-foreground"
              :class="{
                'text-left': !column.align || column.align === 'left',
                'text-center': column.align === 'center',
                'text-right': column.align === 'right',
              }"
            >
              {{ column.label }}
            </th>
          </tr>
        </thead>

        <tbody v-if="rows.length" class="divide-y divide-border bg-background">
          <tr v-for="(row, rowIndex) in rows" :key="rowIndex" class="hover:bg-muted/40">
            <td
              v-for="column in columns"
              :key="column.key"
              class="whitespace-nowrap px-4 py-3 text-foreground"
              :class="{
                'text-left': !column.align || column.align === 'left',
                'text-center': column.align === 'center',
                'text-right': column.align === 'right',
              }"
            >
              <slot :name="`cell-${column.key}`" :row="row" :value="row[column.key]">
                {{ row[column.key] }}
              </slot>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <EmptyState
      v-if="!rows.length"
      :title="emptyTitle"
      :description="emptyDescription"
      class="m-4"
    />
  </div>
</template>

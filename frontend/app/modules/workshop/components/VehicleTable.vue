<script setup lang="ts">
import type { DataTableColumn } from '~/components/ui/organisms/DataTable.vue'
import type { Vehicle } from '../types/vehicle'

const props = defineProps<{
  vehicles: Vehicle[]
}>()

const columns: DataTableColumn[] = [
  { key: 'vehicle', label: 'Veiculo' },
  { key: 'plate', label: 'Placa' },
  { key: 'year', label: 'Ano', align: 'right' },
  { key: 'ownerName', label: 'Proprietario' },
  { key: 'ownerPhone', label: 'Telefone' },
]

const rows = computed(() => {
  return props.vehicles.map((vehicle) => ({
    vehicle: `${vehicle.brand} ${vehicle.model}`,
    plate: vehicle.plate,
    year: vehicle.year,
    ownerName: vehicle.ownerName,
    ownerPhone: vehicle.ownerPhone,
  }))
})
</script>

<template>
  <DataTable
    :columns="columns"
    :rows="rows"
    empty-title="Nenhum veiculo encontrado"
    empty-description="Cadastre veiculos ou ajuste a busca para continuar."
  >
    <template #cell-vehicle="{ value }">
      <p class="truncate font-medium">
        {{ value }}
      </p>
    </template>

    <template #cell-plate="{ value }">
      <span class="inline-flex rounded-md bg-secondary px-2 py-1 text-xs font-medium text-secondary-foreground">
        {{ value }}
      </span>
    </template>
  </DataTable>
</template>

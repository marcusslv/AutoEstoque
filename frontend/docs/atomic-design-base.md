# Atomic Design Base

Esta pasta documenta os componentes visuais compartilhados da Fase 1.

## Regras

- Atoms, molecules, organisms e templates nao chamam API.
- Componentes compartilhados nao conhecem produtos, estoque, veiculos, ordens de servico ou usuarios.
- Regras de dominio ficam em `app/modules`.
- Pages usam templates e componentes de modulo, mantendo pouca logica.

## Camadas

Atoms:

- `AppButton`
- `AppInput`
- `AppLabel`
- `AppTextarea`
- `AppSelect`
- `AppBadge`
- `AppIconButton`
- `AppSpinner`
- `AppSeparator`

Molecules:

- `FormField`
- `SearchInput`
- `StatusBadge`
- `DateRangeFilter`
- `MoneyDisplay`
- `MetricCard`

Organisms:

- `PageHeader`
- `DataTable`
- `FilterToolbar`
- `EntityFormDialog`

Templates:

- `PublicPageTemplate`
- `ListPageTemplate`
- `DetailPageTemplate`
- `DashboardPageTemplate`

Feedback:

- `LoadingState`
- `ErrorState`
- `EmptyState`
- `ForbiddenState`
- `ConfirmDialog`

## Exemplo

```vue
<template>
  <ListPageTemplate title="Produtos" description="Itens cadastrados no estoque">
    <template #actions>
      <AppButton>Novo produto</AppButton>
    </template>

    <template #filters>
      <FilterToolbar v-model:search="search" search-placeholder="Buscar por nome ou SKU" />
    </template>

    <DataTable :columns="columns" :rows="rows" />
  </ListPageTemplate>
</template>
```

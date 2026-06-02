# Exemplo De Arquitetura Front-end Com Atomic Design E Nuxt

Este documento apresenta um exemplo pratico de como aplicar Atomic Design no front-end do AutoEstoque usando Nuxt, sem abandonar a organizacao por modulos de dominio.

A recomendacao e usar uma arquitetura hibrida:

- Atomic Design para componentes reutilizaveis de interface.
- Modulos por dominio para componentes, composables, services e types especificos do produto.

Assim, a interface fica consistente e reutilizavel, mas a regra de negocio continua organizada por contexto.

## 1. Ideia Central

Atomic Design ajuda a organizar a camada visual.

DDD/modulos ajudam a organizar a camada de produto.

No AutoEstoque, isso significa:

- `Button`, `Input`, `Badge` e `DataTable` ficam na base visual compartilhada.
- `ProductForm`, `ServiceOrderDetails` e `RegisterEntryDialog` ficam nos modulos de negocio.

## 2. Estrutura Recomendada

```text
frontend/
  app/
    components/
      ui/
        atoms/
        molecules/
        organisms/
      layout/
        templates/
      feedback/
    modules/
      auth/
        components/
        composables/
        services/
        stores/
        types/
      catalog/
        components/
        composables/
        services/
        types/
      inventory/
        components/
        composables/
        services/
        types/
      workshop/
        components/
        composables/
        services/
        types/
      users/
        components/
        composables/
        services/
        types/
    pages/
    shared/
      api/
      permissions/
      types/
      utils/
```

## 3. Camadas

### 3.1 Atoms

Atoms sao componentes minimos e reutilizaveis.

Eles nao devem conhecer regras do AutoEstoque.

Exemplos:

```text
components/ui/atoms/
  AppButton.vue
  AppInput.vue
  AppLabel.vue
  AppBadge.vue
  AppIconButton.vue
  AppSpinner.vue
  AppSeparator.vue
```

Exemplo:

```vue
<!-- components/ui/atoms/AppButton.vue -->
<script setup lang="ts">
type Variant = 'primary' | 'secondary' | 'danger' | 'ghost'

defineProps<{
  variant?: Variant
  loading?: boolean
  disabled?: boolean
}>()
</script>

<template>
  <button
    :disabled="disabled || loading"
    class="inline-flex h-9 items-center justify-center rounded-md px-3 text-sm font-medium"
  >
    <AppSpinner v-if="loading" class="mr-2" />
    <slot />
  </button>
</template>
```

Observacao:

Em uma implementacao real, esse componente provavelmente sera um wrapper do Shadcn Vue.

### 3.2 Molecules

Molecules combinam atoms em pequenos blocos reutilizaveis.

Ainda devem ser genericas.

Exemplos:

```text
components/ui/molecules/
  SearchInput.vue
  FormField.vue
  StatusBadge.vue
  DateRangeFilter.vue
  ConfirmAction.vue
  MetricCard.vue
```

Exemplo:

```vue
<!-- components/ui/molecules/SearchInput.vue -->
<script setup lang="ts">
defineProps<{
  modelValue: string
  placeholder?: string
}>()

defineEmits<{
  'update:modelValue': [value: string]
}>()
</script>

<template>
  <div class="relative">
    <AppInput
      :model-value="modelValue"
      :placeholder="placeholder ?? 'Buscar'"
      @update:model-value="$emit('update:modelValue', $event)"
    />
  </div>
</template>
```

### 3.3 Organisms

Organisms sao blocos maiores de interface.

Podem ser genericos ou semi-genericos, mas ainda nao devem concentrar regra forte de dominio.

Exemplos:

```text
components/ui/organisms/
  DataTable.vue
  FilterToolbar.vue
  EntityFormDialog.vue
  PageHeader.vue
```

Exemplo:

```vue
<!-- components/ui/organisms/PageHeader.vue -->
<script setup lang="ts">
defineProps<{
  title: string
  description?: string
}>()
</script>

<template>
  <header class="flex items-start justify-between gap-4">
    <div>
      <h1 class="text-xl font-semibold">
        {{ title }}
      </h1>
      <p v-if="description" class="mt-1 text-sm text-muted-foreground">
        {{ description }}
      </p>
    </div>

    <div class="flex items-center gap-2">
      <slot name="actions" />
    </div>
  </header>
</template>
```

### 3.4 Templates

Templates definem estrutura de tela, sem regra de negocio.

Exemplos:

```text
components/layout/templates/
  AuthenticatedTemplate.vue
  PublicTemplate.vue
  ListPageTemplate.vue
  DetailPageTemplate.vue
  DashboardTemplate.vue
```

Exemplo:

```vue
<!-- components/layout/templates/ListPageTemplate.vue -->
<script setup lang="ts">
defineProps<{
  title: string
  description?: string
}>()
</script>

<template>
  <section class="space-y-4">
    <PageHeader :title="title" :description="description">
      <template #actions>
        <slot name="actions" />
      </template>
    </PageHeader>

    <slot name="filters" />

    <div>
      <slot />
    </div>
  </section>
</template>
```

### 3.5 Pages

Pages sao rotas Nuxt.

Elas devem ser finas.

Exemplo:

```vue
<!-- pages/service-orders/[id].vue -->
<script setup lang="ts">
definePageMeta({
  middleware: ['auth', 'workshop-role'],
})

const route = useRoute()
const serviceOrderId = computed(() => String(route.params.id))
</script>

<template>
  <ServiceOrderDetailsPage :service-order-id="serviceOrderId" />
</template>
```

### 3.6 Modules

Modules carregam componentes e logica especifica do AutoEstoque.

Exemplo:

```text
modules/workshop/
  components/
    ServiceOrderDetailsPage.vue
    ServiceOrderSummary.vue
    ServiceOrderPartsTable.vue
    AddPartDialog.vue
    FinishServiceOrderDialog.vue
  composables/
    useServiceOrderDetails.ts
    useServiceOrders.ts
  services/
    workshopApi.ts
  types/
    serviceOrder.ts
    vehicle.ts
```

## 4. Exemplo Completo - Detalhe Da Ordem De Servico

Esta secao mostra como a tela de detalhe da OS ficaria nessa arquitetura.

### 4.1 Rota

```text
pages/service-orders/[id].vue
```

Responsabilidade:

- ler o ID da rota;
- aplicar middleware;
- renderizar o componente de pagina do modulo.

```vue
<script setup lang="ts">
definePageMeta({
  layout: 'authenticated',
  middleware: ['auth', 'workshop-role'],
})

const route = useRoute()
const serviceOrderId = computed(() => String(route.params.id))
</script>

<template>
  <ServiceOrderDetailsPage :service-order-id="serviceOrderId" />
</template>
```

### 4.2 Componente De Pagina Do Modulo

```text
modules/workshop/components/ServiceOrderDetailsPage.vue
```

Responsabilidade:

- carregar detalhe da OS;
- controlar loading;
- controlar erro;
- orquestrar dialogs;
- passar dados para componentes menores.

```vue
<script setup lang="ts">
const props = defineProps<{
  serviceOrderId: string
}>()

const {
  serviceOrder,
  loading,
  error,
  refresh,
  finishServiceOrder,
} = useServiceOrderDetails(props.serviceOrderId)

const addPartOpen = ref(false)
const finishOpen = ref(false)
</script>

<template>
  <DetailPageTemplate
    title="Ordem de servico"
    :description="serviceOrder?.customer_name"
  >
    <template #actions>
      <AppButton
        v-if="serviceOrder?.status === 'open'"
        variant="secondary"
        @click="addPartOpen = true"
      >
        Adicionar peca
      </AppButton>

      <AppButton
        v-if="serviceOrder?.status === 'open'"
        @click="finishOpen = true"
      >
        Finalizar OS
      </AppButton>
    </template>

    <LoadingState v-if="loading" />
    <ErrorState v-else-if="error" :message="error.message" @retry="refresh" />

    <template v-else-if="serviceOrder">
      <ServiceOrderSummary :service-order="serviceOrder" />
      <ServiceOrderPartsTable :parts="serviceOrder.parts" />

      <AddPartDialog
        v-model:open="addPartOpen"
        :service-order-id="serviceOrder.id"
        @created="refresh"
      />

      <FinishServiceOrderDialog
        v-model:open="finishOpen"
        :service-order-id="serviceOrder.id"
        @finished="refresh"
      />
    </template>
  </DetailPageTemplate>
</template>
```

### 4.3 Composable Do Modulo

```text
modules/workshop/composables/useServiceOrderDetails.ts
```

Responsabilidade:

- buscar a OS;
- expor estado;
- executar acoes relacionadas.

```ts
export function useServiceOrderDetails(serviceOrderId: string) {
  const api = useWorkshopApi()

  const serviceOrder = ref<ServiceOrderDetails | null>(null)
  const loading = ref(false)
  const error = ref<ApiError | null>(null)

  async function refresh() {
    loading.value = true
    error.value = null

    try {
      const response = await api.showServiceOrder(serviceOrderId)
      serviceOrder.value = response.data
    } catch (err) {
      error.value = normalizeApiError(err)
    } finally {
      loading.value = false
    }
  }

  async function finishServiceOrder() {
    await api.finishServiceOrder(serviceOrderId)
    await refresh()
  }

  onMounted(refresh)

  return {
    serviceOrder,
    loading,
    error,
    refresh,
    finishServiceOrder,
  }
}
```

### 4.4 Service Do Modulo

```text
modules/workshop/services/workshopApi.ts
```

Responsabilidade:

- encapsular chamadas HTTP do modulo Workshop.

```ts
export function useWorkshopApi() {
  const api = useApiClient()

  return {
    listVehicles(params?: ListVehiclesParams) {
      return api<VehicleListResponse>('/vehicles', { params })
    },

    createVehicle(payload: CreateVehiclePayload) {
      return api<VehicleResponse>('/vehicles', {
        method: 'POST',
        body: payload,
      })
    },

    listServiceOrders(params?: ListServiceOrdersParams) {
      return api<ServiceOrderListResponse>('/service-orders', { params })
    },

    showServiceOrder(serviceOrderId: string) {
      return api<ServiceOrderDetailsResponse>(`/service-orders/${serviceOrderId}`)
    },

    addPartToServiceOrder(serviceOrderId: string, payload: AddPartPayload) {
      return api<AddPartResponse>(`/service-orders/${serviceOrderId}/parts`, {
        method: 'POST',
        body: payload,
      })
    },

    finishServiceOrder(serviceOrderId: string) {
      return api<FinishServiceOrderResponse>(`/service-orders/${serviceOrderId}/finish`, {
        method: 'PATCH',
      })
    },
  }
}
```

### 4.5 Componente De Dominio

```text
modules/workshop/components/ServiceOrderPartsTable.vue
```

Esse componente ja conhece o dominio de OS.

Ele pode usar organismos genericos, mas sua linguagem e de produto.

```vue
<script setup lang="ts">
defineProps<{
  parts: ServiceOrderPart[]
}>()
</script>

<template>
  <DataTable>
    <thead>
      <tr>
        <th>Peca</th>
        <th>SKU</th>
        <th>Qtd.</th>
        <th>Movimentacoes</th>
      </tr>
    </thead>

    <tbody>
      <tr v-for="part in parts" :key="part.id">
        <td>{{ part.product_name }}</td>
        <td>{{ part.product_sku }}</td>
        <td>{{ part.quantity }}</td>
        <td>
          <AppBadge v-if="part.movements_total > 0">
            {{ part.movements_total }} baixa(s)
          </AppBadge>
          <span v-else class="text-muted-foreground">
            Sem baixa
          </span>
        </td>
      </tr>
    </tbody>
  </DataTable>
</template>
```

## 5. Exemplo De Componentes Por Nivel

### 5.1 Produto

```text
atoms:
  AppInput
  AppButton
  AppBadge

molecules:
  FormField
  SearchInput
  MoneyInput

organisms:
  DataTable
  EntityFormDialog

module/catalog:
  ProductForm
  ProductTable
  StockStatusBadge
  ProductEditDialog

page:
  pages/products/index.vue
```

### 5.2 Estoque

```text
atoms:
  AppBadge
  AppButton

molecules:
  SearchInput
  StatusBadge

organisms:
  DataTable
  FilterToolbar

module/catalog:
  StockTable
  StockStatusBadge

page:
  pages/stock.vue
```

### 5.3 Movimentacoes

```text
atoms:
  AppButton
  AppBadge

molecules:
  DateRangeFilter
  FormField

organisms:
  DataTable
  EntityFormDialog

module/inventory:
  MovementHistoryTable
  RegisterEntryDialog
  RegisterOutputDialog
  RegisterAdjustmentDialog
  MovementOriginLink

page:
  pages/inventory/movements.vue
```

### 5.4 Usuarios

```text
atoms:
  AppButton
  AppBadge

molecules:
  RoleBadge
  StatusBadge

organisms:
  DataTable
  EntityFormDialog

module/users:
  UserTable
  UserForm
  DeactivateUserDialog

page:
  pages/users/index.vue
```

## 6. O Que Nao Deve Ir Para Atomic Design

Nem tudo deve virar atom, molecule ou organism.

Nao colocar em `components/ui`:

- regra de criacao de produto;
- regra de finalizar OS;
- chamada de API de estoque;
- permissao especifica de usuario;
- transformacao de dados do backend para uma tela especifica.

Esses pontos pertencem a:

- `modules/{domain}/services`;
- `modules/{domain}/composables`;
- `modules/{domain}/components`;
- `shared/permissions`.

## 7. Regra De Decisao

Use esta regra simples:

### Vai Para Atomic Design Quando

- pode ser usado em varios dominios;
- nao conhece entidade de negocio;
- nao chama API;
- nao sabe o que e produto, OS, veiculo ou usuario;
- representa uma estrutura visual reutilizavel.

Exemplos:

- `AppButton`
- `SearchInput`
- `DataTable`
- `PageHeader`
- `ListPageTemplate`

### Vai Para Modulo Quando

- conhece uma entidade do AutoEstoque;
- usa tipos como `Product`, `Vehicle`, `ServiceOrder`;
- executa acao de negocio;
- chama service do dominio;
- possui texto especifico de negocio.

Exemplos:

- `ProductForm`
- `ServiceOrderDetails`
- `AddPartDialog`
- `RegisterEntryDialog`
- `UserForm`

## 8. Exemplo De Fluxo De Dependencia

Fluxo correto:

```text
page
  -> module component
    -> module composable
      -> module service
        -> shared api client

module component
  -> template
    -> organism
      -> molecule
        -> atom
```

Fluxo que deve ser evitado:

```text
atom
  -> module service

molecule
  -> chamada HTTP

components/ui
  -> regra especifica de OS
```

## 9. Beneficios Para O AutoEstoque

Essa abordagem permite:

- manter consistencia visual;
- reduzir duplicacao de componentes;
- preservar linguagem de dominio;
- facilitar manutencao;
- evitar que Atomic Design vire uma pasta generica sem contexto;
- permitir crescimento do produto sem baguncar a UI.

## 10. Proxima Evolucao

Se essa abordagem for aprovada, os documentos principais devem ser atualizados para refletir esse modelo hibrido:

```text
docs/arquitetura/arquitetura-frontend-nuxt.md
docs/arquitetura/sequencia-implementacao-frontend-nuxt.md
```

Depois disso, a implementacao pode iniciar pela Fase 0 do front-end, ja criando:

```text
components/ui/atoms
components/ui/molecules
components/ui/organisms
components/layout/templates
modules/
```

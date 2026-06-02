# Arquitetura Front-end - Nuxt Com Atomic Design

Este documento define a arquitetura front-end do AutoEstoque usando Nuxt, Vue, TypeScript, Tailwind CSS, Shadcn Vue e Atomic Design.

A proposta oficial para o front-end web e uma arquitetura hibrida:

- **Atomic Design** para organizar componentes visuais reutilizaveis.
- **Modulos por dominio** para organizar regras, services, composables, types e componentes especificos do AutoEstoque.

Essa abordagem evita dois problemas comuns:

- uma pasta `components` grande e dificil de manter;
- um Atomic Design puro demais, onde componentes de negocio ficam sem contexto.

## 1. Objetivo Do Front-end Web

O front-end web sera o painel administrativo da oficina.

Ele deve permitir:

- autenticar usuarios;
- visualizar dashboard;
- gerenciar produtos;
- consultar estoque;
- registrar movimentacoes;
- visualizar alertas;
- gerenciar veiculos;
- gerenciar ordens de servico;
- gerenciar usuarios da oficina;
- respeitar permissoes por perfil.

O web deve priorizar produtividade, clareza operacional e velocidade de uso. O foco nao e uma landing page, mas uma ferramenta de trabalho para oficinas.

## 2. Stack Recomendada

Tecnologias:

- Nuxt 4
- Vue 3
- TypeScript
- Tailwind CSS
- Shadcn Vue
- Pinia
- `$fetch`/ofetch

Responsabilidades:

- Nuxt: rotas, layouts, configuracao e estrutura da aplicacao.
- Vue: componentes e composicao da interface.
- TypeScript: contratos, tipos de dominio e seguranca de integracao.
- Tailwind CSS: estilo utilitario.
- Shadcn Vue: componentes base de UI.
- Pinia: estado de sessao e estados compartilhados.
- `$fetch`/ofetch: comunicacao HTTP com o backend.

## 3. Visao Geral Da Arquitetura

Estrutura conceitual:

```text
pages
  -> modules
    -> composables
      -> services
        -> shared/api

modules
  -> templates
    -> organisms
      -> molecules
        -> atoms
```

Regras:

- `pages` devem ser finas.
- `modules` conhecem o dominio do AutoEstoque.
- `shared/api` concentra comunicacao HTTP.
- `components/ui` concentra Atomic Design.
- `components/layout/templates` concentra estruturas de tela.
- atoms, molecules e organisms nao devem chamar API.

## 4. Estrutura De Pastas Recomendada

```text
frontend/
  app/
    assets/
    components/
      ui/
        atoms/
        molecules/
        organisms/
      layout/
        templates/
        shell/
      feedback/
    layouts/
    middleware/
    modules/
      auth/
        components/
        composables/
        services/
        stores/
        types/
      dashboard/
        components/
        composables/
        services/
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
    plugins/
    shared/
      api/
      auth/
      errors/
      permissions/
      types/
      utils/
```

## 5. Atomic Design No AutoEstoque

Atomic Design sera usado para a camada visual compartilhada.

O objetivo nao e forcar todos os componentes da aplicacao dentro de `atoms`, `molecules` e `organisms`. Componentes que falam a linguagem do negocio devem ficar nos modulos.

## 6. Atoms

Atoms sao os menores blocos de interface.

Caracteristicas:

- reutilizaveis;
- sem regra de negocio;
- sem chamada HTTP;
- sem conhecimento de produto, OS, estoque ou usuario;
- geralmente wrappers de componentes Shadcn Vue.

Exemplos:

```text
components/ui/atoms/
  AppButton.vue
  AppInput.vue
  AppLabel.vue
  AppTextarea.vue
  AppSelect.vue
  AppBadge.vue
  AppIconButton.vue
  AppSpinner.vue
  AppSeparator.vue
  AppAvatar.vue
```

Exemplo:

```vue
<!-- components/ui/atoms/AppButton.vue -->
<script setup lang="ts">
type ButtonVariant = 'primary' | 'secondary' | 'danger' | 'ghost'

withDefaults(defineProps<{
  variant?: ButtonVariant
  loading?: boolean
  disabled?: boolean
}>(), {
  variant: 'primary',
  loading: false,
  disabled: false,
})
</script>

<template>
  <button :disabled="disabled || loading">
    <AppSpinner v-if="loading" />
    <slot />
  </button>
</template>
```

## 7. Molecules

Molecules combinam atoms em pequenas unidades reutilizaveis.

Caracteristicas:

- podem ter uma pequena logica de apresentacao;
- ainda nao conhecem regra de negocio;
- podem ser usadas em varios modulos;
- nao chamam API.

Exemplos:

```text
components/ui/molecules/
  FormField.vue
  SearchInput.vue
  DateRangeFilter.vue
  StatusBadge.vue
  MoneyDisplay.vue
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

## 8. Organisms

Organisms sao blocos maiores de interface.

Caracteristicas:

- combinam atoms e molecules;
- podem estruturar tabelas, filtros, dialogs e cabecalhos;
- ainda devem evitar regra especifica do AutoEstoque;
- podem ser parametrizados por props e slots.

Exemplos:

```text
components/ui/organisms/
  DataTable.vue
  FilterToolbar.vue
  PageHeader.vue
  EntityFormDialog.vue
  EmptyTableState.vue
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
      <p v-if="description" class="text-sm text-muted-foreground">
        {{ description }}
      </p>
    </div>

    <div class="flex items-center gap-2">
      <slot name="actions" />
    </div>
  </header>
</template>
```

## 9. Templates

Templates definem estrutura de pagina.

Caracteristicas:

- organizam slots e regioes;
- nao carregam dados;
- nao chamam API;
- nao conhecem regras de dominio;
- sao usados por pages e module components.

Exemplos:

```text
components/layout/templates/
  PublicPageTemplate.vue
  AuthenticatedPageTemplate.vue
  ListPageTemplate.vue
  DetailPageTemplate.vue
  DashboardPageTemplate.vue
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

    <slot />
  </section>
</template>
```

## 10. Layout Shell

O shell da aplicacao autenticada deve ficar separado dos templates.

Exemplos:

```text
components/layout/shell/
  AppShell.vue
  AppSidebar.vue
  AppHeader.vue
  AppBreadcrumb.vue
  UserMenu.vue
```

Responsabilidades:

- navegacao principal;
- menu por perfil;
- area de usuario;
- estrutura responsiva;
- renderizacao do conteudo autenticado.

## 11. Feedback Components

Componentes de feedback podem ficar fora da hierarquia Atomic para facilitar uso em qualquer camada.

Exemplos:

```text
components/feedback/
  LoadingState.vue
  ErrorState.vue
  EmptyState.vue
  ForbiddenState.vue
  ConfirmDialog.vue
```

Esses componentes devem ser genericos.

## 12. Pages

Pages representam rotas Nuxt.

Regras:

- devem ser pequenas;
- devem aplicar layout e middleware;
- devem obter parametros de rota;
- devem delegar regra para componentes do modulo.

Exemplo:

```vue
<!-- pages/service-orders/[id].vue -->
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

## 13. Modules

Modules organizam tudo que e especifico de um dominio do AutoEstoque.

Modulos principais:

```text
modules/
  auth/
  dashboard/
  catalog/
  inventory/
  workshop/
  users/
```

Cada modulo pode ter:

```text
components/
composables/
services/
types/
stores/
```

Nem todo modulo precisa de todas as pastas.

## 14. Regra De Decisao: Atomic Ou Module?

### Vai Para Atomic Design Quando

- pode ser usado por varios dominios;
- nao conhece entidade de negocio;
- nao chama API;
- nao sabe o que e produto, veiculo, OS, estoque ou usuario;
- representa uma estrutura visual reutilizavel.

Exemplos:

- `AppButton`
- `SearchInput`
- `DataTable`
- `PageHeader`
- `ListPageTemplate`

### Vai Para Module Quando

- conhece entidade do AutoEstoque;
- usa tipos como `Product`, `Vehicle`, `ServiceOrder`;
- executa acao de negocio;
- chama service do dominio;
- possui texto especifico de negocio.

Exemplos:

- `ProductForm`
- `StockTable`
- `ServiceOrderDetailsPage`
- `AddPartDialog`
- `RegisterEntryDialog`
- `UserForm`

## 15. Modulo Auth

Responsavel por:

- login;
- logout;
- persistencia de token;
- usuario autenticado;
- perfil atual;
- protecao de rotas.

Estrutura sugerida:

```text
modules/auth/
  components/
    LoginForm.vue
  composables/
    useAuth.ts
  services/
    authApi.ts
  stores/
    authStore.ts
  types/
    auth.ts
```

Rotas de API:

```text
POST /api/v1/auth/login
POST /api/v1/auth/logout
```

## 16. Modulo Dashboard

Responsavel por:

- indicadores gerais;
- movimentacoes recentes;
- produtos mais consumidos.

Estrutura sugerida:

```text
modules/dashboard/
  components/
    DashboardMetrics.vue
    RecentMovements.vue
    MostConsumedProducts.vue
  composables/
    useDashboard.ts
  services/
    dashboardApi.ts
  types/
    dashboard.ts
```

Rotas de API:

```text
GET /api/v1/dashboard
GET /api/v1/dashboard/most-consumed-products
```

## 17. Modulo Catalog

Responsavel por:

- cadastro de produtos;
- edicao de produtos;
- consulta de estoque.

Estrutura sugerida:

```text
modules/catalog/
  components/
    ProductForm.vue
    ProductTable.vue
    StockTable.vue
    StockStatusBadge.vue
  composables/
    useProducts.ts
    useStock.ts
  services/
    catalogApi.ts
  types/
    product.ts
    stock.ts
```

Rotas de API:

```text
POST /api/v1/products
PATCH /api/v1/products/{product}
GET /api/v1/stock
```

## 18. Modulo Inventory

Responsavel por:

- entradas;
- saidas;
- ajustes;
- historico;
- alertas.

Estrutura sugerida:

```text
modules/inventory/
  components/
    MovementHistoryTable.vue
    RegisterEntryDialog.vue
    RegisterOutputDialog.vue
    RegisterAdjustmentDialog.vue
    InventoryAlertsList.vue
    MovementOriginLink.vue
  composables/
    useMovements.ts
    useInventoryAlerts.ts
  services/
    inventoryApi.ts
  types/
    movement.ts
    alert.ts
```

Rotas de API:

```text
POST /api/v1/inventory/entries
POST /api/v1/inventory/outputs
POST /api/v1/inventory/adjustments
GET /api/v1/inventory/movements
GET /api/v1/inventory/alerts/minimum-stock
GET /api/v1/inventory/alerts/zero-stock
```

## 19. Modulo Workshop

Responsavel por:

- veiculos;
- ordens de servico;
- pecas da OS;
- finalizacao com baixa automatica.

Estrutura sugerida:

```text
modules/workshop/
  components/
    VehicleForm.vue
    VehicleTable.vue
    ServiceOrderTable.vue
    ServiceOrderForm.vue
    ServiceOrderDetailsPage.vue
    ServiceOrderSummary.vue
    ServiceOrderPartsTable.vue
    AddPartDialog.vue
    FinishServiceOrderDialog.vue
  composables/
    useVehicles.ts
    useServiceOrders.ts
    useServiceOrderDetails.ts
  services/
    workshopApi.ts
  types/
    vehicle.ts
    serviceOrder.ts
```

Rotas de API:

```text
GET /api/v1/vehicles
POST /api/v1/vehicles
GET /api/v1/service-orders
POST /api/v1/service-orders
GET /api/v1/service-orders/{serviceOrder}
POST /api/v1/service-orders/{serviceOrder}/parts
PATCH /api/v1/service-orders/{serviceOrder}/finish
```

## 20. Modulo Users

Responsavel por:

- listar usuarios;
- criar usuario;
- editar usuario;
- inativar usuario.

Estrutura sugerida:

```text
modules/users/
  components/
    UserTable.vue
    UserForm.vue
    DeactivateUserDialog.vue
    RoleBadge.vue
  composables/
    useUsers.ts
  services/
    usersApi.ts
  types/
    user.ts
```

Rotas de API:

```text
GET /api/v1/users
POST /api/v1/users
PATCH /api/v1/users/{user}
PATCH /api/v1/users/{user}/deactivate
```

## 21. Shared API

Toda comunicacao HTTP deve passar pelo cliente central.

Estrutura:

```text
shared/api/
  apiClient.ts
  apiErrors.ts
  apiTypes.ts
```

Exemplo:

```ts
export function useApiClient() {
  const config = useRuntimeConfig()
  const auth = useAuthStore()

  return $fetch.create({
    baseURL: config.public.apiBaseUrl,
    onRequest({ options }) {
      if (auth.token) {
        options.headers = {
          ...options.headers,
          Authorization: `Bearer ${auth.token}`,
        }
      }
    },
    onResponseError({ response }) {
      if (response.status === 401) {
        auth.clear()
        navigateTo('/login')
      }
    },
  })
}
```

## 22. Services Por Modulo

Cada modulo deve encapsular chamadas HTTP em um service.

Exemplo:

```ts
// modules/workshop/services/workshopApi.ts
export function useWorkshopApi() {
  const api = useApiClient()

  return {
    listServiceOrders(params?: ListServiceOrdersParams) {
      return api<ServiceOrderListResponse>('/service-orders', { params })
    },

    showServiceOrder(serviceOrderId: string) {
      return api<ServiceOrderDetailsResponse>(`/service-orders/${serviceOrderId}`)
    },

    finishServiceOrder(serviceOrderId: string) {
      return api<FinishServiceOrderResponse>(`/service-orders/${serviceOrderId}/finish`, {
        method: 'PATCH',
      })
    },
  }
}
```

## 23. Composables Por Modulo

Composables orquestram estado de tela e chamadas de service.

Eles podem:

- controlar loading;
- controlar erro;
- aplicar filtros;
- chamar actions;
- expor dados prontos para componentes.

Exemplo:

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

  onMounted(refresh)

  return {
    serviceOrder,
    loading,
    error,
    refresh,
  }
}
```

## 24. Autenticacao

Fluxo:

1. Usuario acessa `/login`.
2. Front envia credenciais para `POST /api/v1/auth/login`.
3. Backend retorna token e usuario.
4. Front persiste token e usuario.
5. Cliente API passa a enviar `Authorization: Bearer`.
6. Logout chama `POST /api/v1/auth/logout`.
7. Front limpa sessao.

Arquivos:

```text
modules/auth/stores/authStore.ts
modules/auth/services/authApi.ts
middleware/auth.ts
middleware/guest.ts
```

## 25. Permissoes Por Perfil

Roles:

```ts
type Role = 'owner' | 'manager' | 'admin' | 'mechanic'
```

Matriz:

```ts
const permissions = {
  backoffice: ['owner', 'manager', 'admin'],
  workshop: ['owner', 'manager', 'admin', 'mechanic'],
}
```

Uso:

- filtrar menu;
- proteger rotas;
- esconder botoes;
- exibir `ForbiddenState`;
- ainda tratar `403` vindo do backend.

Arquivos:

```text
shared/permissions/roles.ts
shared/permissions/permissions.ts
middleware/role.ts
```

## 26. Rotas Do Front-end

Rotas iniciais:

```text
/login
/dashboard
/products
/stock
/inventory/movements
/inventory/alerts
/vehicles
/service-orders
/service-orders/:id
/users
```

Estrutura:

```text
pages/
  login.vue
  dashboard.vue
  products/
    index.vue
  stock.vue
  inventory/
    movements.vue
    alerts.vue
  vehicles/
    index.vue
  service-orders/
    index.vue
    [id].vue
  users/
    index.vue
```

## 27. Tratamento De Erros

Erros principais:

- `401 Unauthorized`: limpar sessao e redirecionar para login.
- `403 Forbidden`: exibir acesso negado.
- `404 Not Found`: exibir recurso nao encontrado.
- `409 Conflict`: exibir conflito de regra de negocio.
- `422 Unprocessable Entity`: mapear erros para formulario.
- `500 Internal Server Error`: exibir erro generico.

## 28. Tipagem

A OpenAPI do backend e o contrato de referencia:

```text
backend/docs/openapi.yaml
```

Para o MVP, os tipos podem ser criados manualmente por modulo.

Depois, pode evoluir para geracao automatica.

Exemplo:

```ts
export type Product = {
  id: string
  tenant_id: string
  name: string
  sku: string
  barcode: string | null
  category: string | null
  brand: string | null
  supplier: string | null
  minimum_stock: number
  cost_in_cents: number
  currency: 'BRL'
}
```

## 29. Design Da Interface

O AutoEstoque e uma ferramenta operacional.

Direcao visual:

- interface limpa;
- densidade moderada;
- tabelas escaneaveis;
- filtros objetivos;
- dialogs para acoes rapidas;
- badges para status;
- feedback claro para erros e sucesso;
- menus orientados por perfil.

Evitar:

- landing page dentro do app;
- hero sections;
- excesso de cards decorativos;
- textos explicativos longos;
- componentes visualmente bonitos mas pouco eficientes.

## 30. Exemplo De Tela: Detalhe Da OS

Fluxo:

```text
pages/service-orders/[id].vue
  -> modules/workshop/components/ServiceOrderDetailsPage.vue
    -> modules/workshop/composables/useServiceOrderDetails.ts
      -> modules/workshop/services/workshopApi.ts
        -> shared/api/apiClient.ts

ServiceOrderDetailsPage.vue
  -> DetailPageTemplate.vue
    -> PageHeader.vue
    -> ServiceOrderSummary.vue
    -> ServiceOrderPartsTable.vue
    -> AddPartDialog.vue
    -> FinishServiceOrderDialog.vue
```

Nesse exemplo:

- `DetailPageTemplate` e template.
- `PageHeader` e organism.
- `AppButton` e atom.
- `ServiceOrderSummary` e componente de dominio.
- `ServiceOrderPartsTable` e componente de dominio.
- `AddPartDialog` e componente de dominio.
- `FinishServiceOrderDialog` e componente de dominio.

## 31. Sequencia De Implementacao

1. Setup Nuxt.
2. Tailwind.
3. Shadcn Vue.
4. Pinia.
5. Estrutura Atomic Design.
6. Layout publico e autenticado.
7. Cliente API.
8. Auth.
9. Permissoes.
10. Dashboard.
11. Estoque.
12. Produtos.
13. Veiculos.
14. Ordens de servico.
15. Movimentacoes.
16. Alertas.
17. Usuarios.
18. Refinamentos de UX.
19. Testes.

## 32. Proximo Passo

O proximo passo pratico e iniciar o setup do front-end em:

```text
frontend/
```

Primeira entrega:

- Nuxt rodando;
- Tailwind configurado;
- Shadcn Vue configurado;
- Pinia configurado;
- estrutura Atomic Design criada;
- tela de login inicial;
- cliente API central.

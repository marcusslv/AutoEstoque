# Arquitetura Front-end - Nuxt

Este documento define a proposta inicial de arquitetura do front-end web do AutoEstoque usando Nuxt, Vue, Tailwind CSS e Shadcn Vue.

O objetivo e criar uma base simples, modular e facil de evoluir, mantendo alinhamento com a arquitetura do backend, que usa Clean Architecture, DDD e modulos por dominio.

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

O aplicativo mobile continua sendo pensado para consulta rapida, leitura de codigo de barras e operacoes de campo. O web deve priorizar visao gerencial, operacao administrativa e fluxos mais completos.

## 2. Stack Recomendada

Tecnologias:

- Nuxt 4
- Vue 3
- TypeScript
- Tailwind CSS
- Shadcn Vue
- Pinia
- ofetch ou `$fetch`

Responsabilidades:

- Nuxt: estrutura da aplicacao, rotas, layouts e SSR/SPA.
- Vue: componentes e composicao de telas.
- TypeScript: contratos e seguranca de tipos.
- Tailwind: estilos utilitarios.
- Shadcn Vue: componentes base de interface.
- Pinia: estado de sessao e estados compartilhados.
- `$fetch`/ofetch: comunicacao HTTP com o backend.

## 3. Principios De Arquitetura

### 3.1 Modularidade Por Dominio

A estrutura deve ser organizada por contexto de negocio, nao apenas por tipo tecnico.

Modulos principais:

- `auth`
- `dashboard`
- `catalog`
- `inventory`
- `workshop`
- `users`

Cada modulo deve concentrar seus componentes, composables, services e types.

### 3.2 Pages Finas

As paginas Nuxt devem ser simples.

Elas devem:

- definir a rota;
- aplicar middleware quando necessario;
- montar o componente principal da tela;
- evitar conter regra complexa de apresentacao.

### 3.3 Cliente API Centralizado

Toda comunicacao com o backend deve passar por um cliente HTTP central.

Esse cliente deve:

- aplicar `baseURL`;
- anexar `Authorization: Bearer`;
- tratar erros comuns;
- padronizar respostas;
- permitir tipagem por endpoint.

### 3.4 Backend Como Fonte Da Verdade

O front-end pode esconder menus e botoes por perfil, mas a seguranca real fica no backend.

O front-end deve:

- melhorar experiencia do usuario;
- evitar acoes indisponiveis;
- exibir mensagens claras para `403 Forbidden`.

O backend deve:

- validar token;
- resolver tenant;
- aplicar permissoes por perfil;
- validar payloads.

## 4. Estrutura De Pastas Recomendada

Estrutura alvo:

```text
frontend/
  app/
    assets/
    components/
      ui/
      layout/
      feedback/
    composables/
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

## 5. Responsabilidades Das Pastas

### 5.1 `components/ui`

Componentes base vindos do Shadcn Vue ou wrappers internos.

Exemplos:

- `Button`
- `Input`
- `Select`
- `Dialog`
- `Table`
- `Badge`
- `DropdownMenu`
- `Toast`

Esses componentes nao devem conhecer regras do AutoEstoque.

### 5.2 `components/layout`

Componentes estruturais.

Exemplos:

- `AppShell`
- `AppSidebar`
- `AppHeader`
- `AppBreadcrumb`
- `UserMenu`
- `MobileNavigation`

### 5.3 `components/feedback`

Componentes de estado e retorno visual.

Exemplos:

- `EmptyState`
- `LoadingState`
- `ErrorState`
- `ForbiddenState`
- `ConfirmDialog`

### 5.4 `modules`

Cada modulo representa um contexto de produto.

Exemplo:

```text
modules/catalog/
  components/
    ProductForm.vue
    ProductTable.vue
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

### 5.5 `shared/api`

Infraestrutura HTTP compartilhada.

Arquivos sugeridos:

```text
shared/api/
  apiClient.ts
  apiErrors.ts
  apiTypes.ts
```

### 5.6 `shared/permissions`

Matriz de permissoes usada para menus, rotas e botoes.

Arquivos sugeridos:

```text
shared/permissions/
  roles.ts
  permissions.ts
```

## 6. Modulos Do Produto

### 6.1 Auth

Responsavel por:

- login;
- logout;
- persistencia do token;
- usuario autenticado;
- perfil atual;
- protecao de rotas.

Rotas relacionadas:

- `POST /api/v1/auth/login`
- `POST /api/v1/auth/logout`

Estado principal:

```ts
type AuthUser = {
  id: string
  tenant_id: string
  name: string
  email: string
  role: Role
}
```

### 6.2 Dashboard

Responsavel por:

- indicadores gerais;
- produtos abaixo do minimo;
- produtos zerados;
- valor total em estoque;
- movimentacoes recentes;
- produtos mais consumidos.

Rotas relacionadas:

- `GET /api/v1/dashboard`
- `GET /api/v1/dashboard/most-consumed-products`

### 6.3 Catalog

Responsavel por:

- cadastro de produtos;
- edicao de produtos;
- consulta de estoque.

Rotas relacionadas:

- `POST /api/v1/products`
- `PATCH /api/v1/products/{product}`
- `GET /api/v1/stock`

### 6.4 Inventory

Responsavel por:

- entrada de estoque;
- saida de estoque;
- ajuste manual;
- historico de movimentacoes;
- alertas de minimo e zerado.

Rotas relacionadas:

- `POST /api/v1/inventory/entries`
- `POST /api/v1/inventory/outputs`
- `POST /api/v1/inventory/adjustments`
- `GET /api/v1/inventory/movements`
- `GET /api/v1/inventory/alerts/minimum-stock`
- `GET /api/v1/inventory/alerts/zero-stock`

### 6.5 Workshop

Responsavel por:

- cadastro de veiculos;
- listagem de veiculos;
- criacao de OS;
- listagem de OS;
- detalhe da OS;
- adicionar pecas a OS;
- finalizar OS com baixa automatica.

Rotas relacionadas:

- `GET /api/v1/vehicles`
- `POST /api/v1/vehicles`
- `GET /api/v1/service-orders`
- `POST /api/v1/service-orders`
- `GET /api/v1/service-orders/{serviceOrder}`
- `POST /api/v1/service-orders/{serviceOrder}/parts`
- `PATCH /api/v1/service-orders/{serviceOrder}/finish`

### 6.6 Users

Responsavel por:

- listar usuarios;
- criar usuario;
- editar usuario;
- inativar usuario.

Rotas relacionadas:

- `GET /api/v1/users`
- `POST /api/v1/users`
- `PATCH /api/v1/users/{user}`
- `PATCH /api/v1/users/{user}/deactivate`

## 7. Rotas Do Front-end

Rotas iniciais recomendadas:

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

Possivel estrutura Nuxt:

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

## 8. Layouts

### 8.1 Layout Publico

Usado por:

- `/login`

Caracteristicas:

- sem sidebar;
- conteudo centralizado;
- foco em formulario.

### 8.2 Layout Autenticado

Usado pelas rotas internas.

Elementos:

- sidebar principal;
- header;
- menu do usuario;
- breadcrumb;
- area de conteudo;
- suporte responsivo mobile.

## 9. Autenticacao

Fluxo:

1. Usuario acessa `/login`.
2. Front envia email e senha para `POST /api/v1/auth/login`.
3. Backend retorna `access_token` e dados do usuario.
4. Front persiste token e usuario.
5. Front redireciona para `/dashboard`.
6. Cliente API envia `Authorization: Bearer {token}`.
7. Logout chama `POST /api/v1/auth/logout`.
8. Front limpa sessao local.

### 9.1 Persistencia De Sessao

Opcoes:

- cookie HTTP-only, se houver suporte server-side futuro;
- cookie client-side;
- localStorage.

Para o MVP web, pode iniciar com cookie client-side ou localStorage, desde que o token seja removido no logout.

Recomendacao inicial:

- usar cookie via composable do Nuxt para facilitar middleware;
- armazenar dados do usuario em Pinia;
- reidratar estado ao carregar a aplicacao.

## 10. Permissoes Por Perfil

Roles suportados:

```ts
type Role = 'owner' | 'manager' | 'admin' | 'mechanic'
```

Matriz inicial:

```ts
const permissions = {
  backoffice: ['owner', 'manager', 'admin'],
  workshop: ['owner', 'manager', 'admin', 'mechanic'],
}
```

Backoffice:

- usuarios;
- dashboard;
- produtos;
- movimentacoes manuais;
- historico de movimentacoes;
- alertas.

Workshop:

- estoque para consulta;
- veiculos;
- ordens de servico;
- adicionar pecas;
- finalizar OS.

Uso no front:

- proteger rotas;
- esconder menus;
- esconder botoes;
- exibir estado de acesso negado.

O backend continua sendo a fonte real de autorizacao.

## 11. Cliente API

Arquivo sugerido:

```text
shared/api/apiClient.ts
```

Exemplo conceitual:

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

## 12. Tratamento De Erros

Erros principais:

- `401 Unauthorized`: token ausente, invalido ou expirado.
- `403 Forbidden`: usuario sem permissao.
- `404 Not Found`: recurso nao encontrado.
- `409 Conflict`: conflito de regra de negocio.
- `422 Unprocessable Entity`: erro de validacao.
- `500 Internal Server Error`: erro inesperado.

Recomendacao:

- `401`: limpar sessao e redirecionar para login.
- `403`: mostrar tela ou mensagem de acesso negado.
- `422`: mapear erros para formulario.
- `409`: mostrar mensagem de regra de negocio.
- `500`: mostrar erro generico com opcao de tentar novamente.

## 13. Tipagem Dos Contratos

Os tipos devem refletir a OpenAPI:

```text
backend/docs/openapi.yaml
```

Para o MVP, pode criar tipos manualmente por modulo.

Depois, pode evoluir para geracao automatica a partir da OpenAPI.

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

## 14. Padrao De Services

Cada modulo deve ter um service para chamadas HTTP.

Exemplo:

```text
modules/catalog/services/catalogApi.ts
```

Exemplo conceitual:

```ts
export function useCatalogApi() {
  const api = useApiClient()

  return {
    listStock(params?: ListStockParams) {
      return api<StockListResponse>('/stock', { params })
    },

    createProduct(payload: CreateProductPayload) {
      return api<ProductResponse>('/products', {
        method: 'POST',
        body: payload,
      })
    },
  }
}
```

## 15. Padrao De Composables

Composables devem orquestrar estado de tela e chamadas de service.

Exemplo:

```text
modules/catalog/composables/useStock.ts
```

Responsabilidades:

- carregar dados;
- controlar loading;
- controlar erro;
- aplicar filtros;
- expor acoes para componentes.

Eles nao devem conter layout ou markup.

## 16. Formularios

Recomendacao:

- usar componentes Shadcn Vue;
- validar no front apenas o necessario para UX;
- manter validacao real no backend;
- mapear erros `422` por campo.

Padrao de resposta esperada:

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "name": ["The name field is required."]
  }
}
```

## 17. Design Da Interface

O AutoEstoque e uma ferramenta operacional.

Direcao visual:

- interface limpa;
- densidade moderada de informacao;
- tabelas escaneaveis;
- botoes de acao claros;
- badges para status;
- filtros no topo das listagens;
- dialogs para criacao rapida;
- telas focadas em produtividade.

Evitar:

- layout de landing page dentro do app;
- excesso de cards decorativos;
- hero sections;
- textos explicativos longos;
- paleta visual de uma unica cor.

## 18. Componentes-Chave Por Tela

### Login

- formulario de email e senha;
- estado de erro;
- estado de loading.

### Dashboard

- indicadores principais;
- movimentacoes recentes;
- produtos mais consumidos;
- alertas resumidos.

### Produtos

- tabela de produtos;
- busca;
- formulario de cadastro;
- formulario de edicao;
- badges de estoque.

### Estoque

- tabela de estoque;
- busca;
- indicador de saldo;
- status `available`, `minimum`, `zero`.

### Movimentacoes

- filtros;
- historico;
- origem da movimentacao;
- link para OS quando houver vinculo.

### Veiculos

- listagem;
- busca;
- cadastro.

### Ordens De Servico

- listagem por status;
- busca por cliente, placa ou servico;
- detalhe da OS;
- pecas adicionadas;
- movimentacoes geradas;
- acao de finalizar.

### Usuarios

- listagem;
- criacao;
- edicao de perfil;
- inativacao.

## 19. Sequencia Recomendada De Implementacao

1. Criar projeto Nuxt.
2. Configurar Tailwind e Shadcn Vue.
3. Criar layout publico e autenticado.
4. Criar cliente API central.
5. Implementar auth store.
6. Implementar login.
7. Implementar middleware de autenticacao.
8. Implementar matriz de permissoes.
9. Implementar dashboard.
10. Implementar estoque.
11. Implementar produtos.
12. Implementar veiculos.
13. Implementar ordens de servico.
14. Implementar usuarios.
15. Implementar historico e alertas.

## 20. Variaveis De Ambiente

Variaveis recomendadas:

```env
NUXT_PUBLIC_API_BASE_URL=http://localhost:8080/api/v1
```

## 21. Relacao Com OpenAPI

A OpenAPI do backend deve ser usada como contrato de referencia:

```text
backend/docs/openapi.yaml
```

Uso recomendado:

- consultar contratos durante implementacao;
- importar no Postman/Insomnia;
- futuramente gerar types TypeScript.

## 22. Proximo Passo

O proximo passo recomendado e criar o setup do projeto Nuxt dentro da pasta:

```text
frontend/
```

Primeira entrega pratica:

- app Nuxt rodando;
- Tailwind configurado;
- Shadcn Vue configurado;
- tela de login;
- cliente API;
- autenticacao integrada ao backend;
- layout autenticado basico.

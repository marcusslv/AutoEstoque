# Sequencia De Implementacao Do Front-end Nuxt Com Atomic Design

Este documento define a ordem recomendada para implementar o front-end web do AutoEstoque usando Nuxt, Vue, TypeScript, Tailwind CSS, Shadcn Vue, Pinia e Atomic Design.

A arquitetura oficial do front-end sera hibrida:

- **Atomic Design** para componentes visuais reutilizaveis.
- **Modulos por dominio** para regras, services, composables, stores, types e componentes especificos do AutoEstoque.

O objetivo e construir o painel administrativo de forma incremental, conectado ao backend real e com uma base visual consistente desde o inicio.

## 1. Principios Da Sequencia

A implementacao deve seguir estes principios:

- iniciar pela base tecnica;
- criar Atomic Design antes das telas de dominio;
- conectar cedo com a API real;
- manter pages finas;
- concentrar regra de negocio nos modulos;
- proteger rotas desde o inicio;
- respeitar permissoes por perfil;
- entregar fatias pequenas e testaveis;
- evitar telas grandes antes de layout, auth e cliente API estarem prontos.

## 2. Dependencias De Backend

Contrato principal:

```text
backend/docs/openapi.yaml
```

API base local:

```text
http://localhost:8080/api/v1
```

Usuarios demo criados pelo seeder:

| Perfil | E-mail | Senha |
| --- | --- | --- |
| owner | owner@autoestoque.test | password |
| manager | manager@autoestoque.test | password |
| admin | admin@autoestoque.test | password |
| mechanic | mechanic@autoestoque.test | password |

## 3. Estrutura Alvo

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

## 4. Fase 0 - Setup Tecnico Do Front-end

Objetivo:

Criar o projeto Nuxt e preparar a base tecnica.

Entregas:

- criar projeto Nuxt em `frontend/`;
- configurar TypeScript;
- configurar Tailwind CSS;
- configurar Shadcn Vue;
- configurar Pinia;
- configurar variavel `NUXT_PUBLIC_API_BASE_URL`;
- criar estrutura de pastas da arquitetura;
- criar pagina inicial temporaria;
- validar servidor local.

Pastas esperadas nesta fase:

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
        shell/
      feedback/
    layouts/
    middleware/
    modules/
    pages/
    plugins/
    shared/
      api/
      permissions/
      types/
      utils/
  nuxt.config.ts
  package.json
```

Critérios de aceite:

- `npm install` executa com sucesso;
- `npm run dev` sobe o front-end;
- Tailwind funciona em uma pagina simples;
- Shadcn Vue possui ao menos um componente instalado;
- Pinia esta registrado;
- `NUXT_PUBLIC_API_BASE_URL` esta configurado;
- estrutura Atomic Design esta criada.

## 5. Fase 1 - Atomic Design Base

Objetivo:

Criar os componentes visuais compartilhados antes das telas de negocio.

Entregas:

- atoms principais;
- molecules principais;
- organisms principais;
- templates principais;
- feedback components;
- documentacao breve de uso dos componentes.

Atoms sugeridos:

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
```

Molecules sugeridos:

```text
components/ui/molecules/
  FormField.vue
  SearchInput.vue
  StatusBadge.vue
  DateRangeFilter.vue
  MoneyDisplay.vue
  MetricCard.vue
```

Organisms sugeridos:

```text
components/ui/organisms/
  PageHeader.vue
  DataTable.vue
  FilterToolbar.vue
  EntityFormDialog.vue
```

Templates sugeridos:

```text
components/layout/templates/
  PublicPageTemplate.vue
  ListPageTemplate.vue
  DetailPageTemplate.vue
  DashboardPageTemplate.vue
```

Feedback sugeridos:

```text
components/feedback/
  LoadingState.vue
  ErrorState.vue
  EmptyState.vue
  ForbiddenState.vue
  ConfirmDialog.vue
```

Critérios de aceite:

- atoms nao conhecem dominio;
- molecules nao chamam API;
- organisms usam slots/props para serem reutilizaveis;
- templates nao carregam dados;
- componentes base podem ser usados em uma pagina temporaria.

## 6. Fase 2 - Layout Shell

Objetivo:

Criar a estrutura visual principal do painel autenticado.

Entregas:

- layout publico;
- layout autenticado;
- `AppShell`;
- sidebar;
- header;
- menu do usuario;
- breadcrumb opcional;
- comportamento responsivo.

Arquivos sugeridos:

```text
layouts/public.vue
layouts/authenticated.vue
components/layout/shell/AppShell.vue
components/layout/shell/AppSidebar.vue
components/layout/shell/AppHeader.vue
components/layout/shell/UserMenu.vue
```

Rotas iniciais:

```text
/login
/dashboard
```

Critérios de aceite:

- layout publico nao exibe sidebar;
- layout autenticado exibe sidebar e header;
- menu principal ja possui placeholders das telas futuras;
- interface funciona em desktop e mobile.

## 7. Fase 3 - Cliente API E Tratamento De Erros

Objetivo:

Criar a camada central de comunicacao com o backend.

Entregas:

- `shared/api/apiClient.ts`;
- `shared/api/apiErrors.ts`;
- `shared/api/apiTypes.ts`;
- tratamento de `401`;
- tratamento de `403`;
- tratamento de `409`;
- tratamento de `422`;
- tratamento generico de `500`;
- helper para mensagens de erro.

Critérios de aceite:

- cliente usa `NUXT_PUBLIC_API_BASE_URL`;
- cliente envia `Authorization: Bearer` quando houver token;
- erro `401` limpa sessao;
- erro `403` pode acionar estado de acesso negado;
- erro `422` pode ser mapeado para formularios.

## 8. Fase 4 - Autenticacao

Objetivo:

Permitir login, logout e persistencia de sessao.

Rotas de API:

```text
POST /api/v1/auth/login
POST /api/v1/auth/logout
```

Entregas:

- tela de login;
- `authStore`;
- persistencia do token;
- persistencia do usuario autenticado;
- logout integrado ao backend;
- middleware `auth`;
- middleware `guest`.

Arquivos sugeridos:

```text
modules/auth/components/LoginForm.vue
modules/auth/composables/useAuth.ts
modules/auth/services/authApi.ts
modules/auth/stores/authStore.ts
modules/auth/types/auth.ts
middleware/auth.ts
middleware/guest.ts
pages/login.vue
```

Critérios de aceite:

- login funciona com credenciais demo;
- token e usado nas chamadas autenticadas;
- logout revoga token;
- rota interna sem token redireciona para `/login`;
- usuario autenticado nao permanece em `/login`.

## 9. Fase 5 - Permissoes Por Perfil

Objetivo:

Aplicar no front-end a matriz de permissoes do backend.

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

Entregas:

- helper `canAccess`;
- middleware de permissao;
- menus filtrados por perfil;
- botoes filtrados por perfil;
- tela de acesso negado.

Arquivos sugeridos:

```text
shared/permissions/roles.ts
shared/permissions/permissions.ts
middleware/role.ts
```

Critérios de aceite:

- `mechanic` nao ve menus de usuarios, produtos e movimentacoes manuais;
- `admin` ve menus de backoffice;
- acesso direto a rota proibida exibe `ForbiddenState`;
- backend continua retornando `403` se o usuario tentar forcar chamada proibida.

## 10. Fase 6 - Dashboard

Objetivo:

Criar a primeira tela autenticada de valor gerencial usando a base Atomic Design.

Rotas de API:

```text
GET /api/v1/dashboard
GET /api/v1/dashboard/most-consumed-products
```

Entregas:

- pagina `/dashboard`;
- cards de indicadores usando `MetricCard`;
- lista de movimentacoes recentes;
- ranking de produtos mais consumidos;
- estados de loading, erro e vazio.

Arquivos sugeridos:

```text
modules/dashboard/services/dashboardApi.ts
modules/dashboard/composables/useDashboard.ts
modules/dashboard/components/DashboardMetrics.vue
modules/dashboard/components/RecentMovements.vue
modules/dashboard/components/MostConsumedProducts.vue
pages/dashboard.vue
```

Critérios de aceite:

- dashboard carrega com usuario `admin`;
- usuario `mechanic` nao acessa dashboard;
- indicadores batem com dados do backend;
- componentes de feedback sao usados corretamente.

## 11. Fase 7 - Consulta De Estoque

Objetivo:

Permitir que todos os perfis operacionais consultem estoque.

Rota de API:

```text
GET /api/v1/stock
```

Entregas:

- tela `/stock`;
- busca por produto com `SearchInput`;
- tabela de produtos com `DataTable`;
- status visual do estoque;
- destaque para estoque zerado e abaixo do minimo.

Arquivos sugeridos:

```text
modules/catalog/services/catalogApi.ts
modules/catalog/composables/useStock.ts
modules/catalog/components/StockTable.vue
modules/catalog/components/StockStatusBadge.vue
pages/stock.vue
```

Critérios de aceite:

- `mechanic` acessa estoque;
- busca funciona;
- produtos sem saldo aparecem como zero;
- status e quantidade ficam claros.

## 12. Fase 8 - Produtos

Objetivo:

Implementar manutencao basica de produtos.

Rotas de API:

```text
POST /api/v1/products
PATCH /api/v1/products/{product}
GET /api/v1/stock
```

Entregas:

- tela `/products`;
- listagem baseada em estoque/produtos;
- formulario de criacao;
- formulario de edicao;
- dialogs usando organisms/templates;
- tratamento de erros `422`;
- restricao por perfil.

Arquivos sugeridos:

```text
modules/catalog/components/ProductForm.vue
modules/catalog/components/ProductTable.vue
modules/catalog/components/ProductDialog.vue
modules/catalog/composables/useProducts.ts
pages/products/index.vue
```

Critérios de aceite:

- `admin` cria produto;
- `admin` edita produto;
- `mechanic` nao acessa produtos;
- erros de SKU/codigo de barras duplicados sao exibidos.

## 13. Fase 9 - Veiculos

Objetivo:

Implementar cadastro e consulta de veiculos.

Rotas de API:

```text
GET /api/v1/vehicles
POST /api/v1/vehicles
```

Entregas:

- tela `/vehicles`;
- busca;
- tabela de veiculos;
- formulario de cadastro;
- dialog de cadastro;
- tratamento de placa duplicada.

Arquivos sugeridos:

```text
modules/workshop/services/workshopApi.ts
modules/workshop/components/VehicleForm.vue
modules/workshop/components/VehicleTable.vue
modules/workshop/components/VehicleDialog.vue
modules/workshop/composables/useVehicles.ts
pages/vehicles/index.vue
```

Critérios de aceite:

- todos os perfis operacionais acessam veiculos;
- cadastro funciona;
- busca por placa, modelo ou proprietario funciona;
- erro de placa duplicada e exibido.

## 14. Fase 10 - Ordens De Servico

Objetivo:

Implementar o fluxo operacional principal da oficina.

Rotas de API:

```text
GET /api/v1/service-orders
POST /api/v1/service-orders
GET /api/v1/service-orders/{serviceOrder}
POST /api/v1/service-orders/{serviceOrder}/parts
PATCH /api/v1/service-orders/{serviceOrder}/finish
```

Entregas:

- tela `/service-orders`;
- listagem por status;
- busca;
- formulario de criacao de OS;
- detalhe da OS;
- adicionar pecas;
- finalizar OS;
- exibir movimentacoes geradas por peca;
- bloquear acoes quando OS estiver finalizada.

Arquivos sugeridos:

```text
modules/workshop/components/ServiceOrderTable.vue
modules/workshop/components/ServiceOrderForm.vue
modules/workshop/components/ServiceOrderDetailsPage.vue
modules/workshop/components/ServiceOrderSummary.vue
modules/workshop/components/ServiceOrderPartsTable.vue
modules/workshop/components/AddPartDialog.vue
modules/workshop/components/FinishServiceOrderDialog.vue
modules/workshop/composables/useServiceOrders.ts
modules/workshop/composables/useServiceOrderDetails.ts
pages/service-orders/index.vue
pages/service-orders/[id].vue
```

Critérios de aceite:

- criar OS funciona;
- adicionar peca funciona;
- finalizar OS baixa estoque;
- detalhe da OS mostra movimentacoes vinculadas;
- erro de estoque insuficiente e exibido;
- OS finalizada nao permite nova finalizacao.

## 15. Fase 11 - Movimentacoes De Estoque

Objetivo:

Implementar entradas, saidas, ajustes e historico.

Rotas de API:

```text
POST /api/v1/inventory/entries
POST /api/v1/inventory/outputs
POST /api/v1/inventory/adjustments
GET /api/v1/inventory/movements
```

Entregas:

- tela `/inventory/movements`;
- historico com filtros;
- dialog de entrada;
- dialog de saida;
- dialog de ajuste;
- origem da movimentacao quando vinculada a OS;
- tratamento de estoque insuficiente.

Arquivos sugeridos:

```text
modules/inventory/services/inventoryApi.ts
modules/inventory/composables/useMovements.ts
modules/inventory/components/MovementHistoryTable.vue
modules/inventory/components/MovementOriginLink.vue
modules/inventory/components/RegisterEntryDialog.vue
modules/inventory/components/RegisterOutputDialog.vue
modules/inventory/components/RegisterAdjustmentDialog.vue
pages/inventory/movements.vue
```

Critérios de aceite:

- `admin` registra entrada;
- `admin` registra saida;
- `admin` registra ajuste;
- `mechanic` nao acessa movimentacoes manuais;
- historico mostra OS vinculada quando existir.

## 16. Fase 12 - Alertas De Estoque

Objetivo:

Exibir alertas operacionais de reposicao.

Rotas de API:

```text
GET /api/v1/inventory/alerts/minimum-stock
GET /api/v1/inventory/alerts/zero-stock
```

Entregas:

- tela `/inventory/alerts`;
- aba ou filtro para minimo e zerado;
- lista de alertas;
- link para produto/estoque;
- destaque visual por criticidade.

Arquivos sugeridos:

```text
modules/inventory/composables/useInventoryAlerts.ts
modules/inventory/components/InventoryAlertsList.vue
pages/inventory/alerts.vue
```

Critérios de aceite:

- alertas abaixo do minimo aparecem;
- alertas zerados aparecem;
- `mechanic` nao acessa a tela;
- estado vazio e tratado.

## 17. Fase 13 - Usuarios

Objetivo:

Implementar gestao de usuarios da oficina.

Rotas de API:

```text
GET /api/v1/users
POST /api/v1/users
PATCH /api/v1/users/{user}
PATCH /api/v1/users/{user}/deactivate
```

Entregas:

- tela `/users`;
- listagem de usuarios;
- criacao;
- edicao;
- inativacao;
- badge por perfil e status;
- restricao para backoffice.

Arquivos sugeridos:

```text
modules/users/services/usersApi.ts
modules/users/composables/useUsers.ts
modules/users/components/UserTable.vue
modules/users/components/UserForm.vue
modules/users/components/UserDialog.vue
modules/users/components/DeactivateUserDialog.vue
modules/users/components/RoleBadge.vue
pages/users/index.vue
```

Critérios de aceite:

- `owner`, `manager` e `admin` acessam;
- `mechanic` nao acessa;
- limite de usuarios retorna mensagem clara;
- usuario inativado aparece com status correto.

## 18. Fase 14 - Refinamento De UX

Objetivo:

Melhorar experiencia geral antes de considerar o MVP web navegavel.

Entregas:

- toasts padronizados;
- dialogs de confirmacao;
- breadcrumbs;
- responsividade;
- atalhos de navegacao entre entidades;
- estados vazios melhores;
- mascaras de placa e telefone;
- formatacao de moeda;
- formatacao de data;
- revisao de contraste e acessibilidade.

Critérios de aceite:

- telas principais funcionam em desktop e mobile;
- erros sao compreensiveis;
- acoes destrutivas pedem confirmacao;
- usuario sempre entende o resultado de uma acao.

## 19. Fase 15 - Testes Do Front-end

Objetivo:

Adicionar testes onde houver maior risco de regressao.

Entregas:

- infraestrutura de testes com Vitest;
- infraestrutura de testes E2E com Playwright;
- setup de mocks para composables globais do Nuxt;
- testes de auth store;
- testes de permissoes;
- testes de atoms/molecules criticos;
- testes de services com mocks;
- teste E2E do fluxo de login;
- documentacao dos comandos de teste no README do front-end.

Ferramentas sugeridas:

- Vitest;
- Vue Test Utils;
- Playwright.

Critérios de aceite:

- login testado;
- permissao por perfil testada;
- services criticos testados com mocks;
- componente visual critico testado;
- pipeline consegue rodar testes.

Observacao:

O E2E de criar OS e finalizar deve entrar como ampliacao da suite apos estabilizar massa de dados/mocks para o fluxo completo de oficina.

## 20. Ordem Resumida

| Ordem | Fase | Entrega |
| --- | --- | --- |
| 0 | Setup tecnico | Nuxt, Tailwind, Shadcn, Pinia |
| 1 | Atomic Design base | Atoms, molecules, organisms, templates |
| 2 | Layout shell | Layout publico e autenticado |
| 3 | API | Cliente HTTP e erros |
| 4 | Auth | Login, logout e sessao |
| 5 | Permissoes | Rotas e menus por perfil |
| 6 | Dashboard | Indicadores gerenciais |
| 7 | Estoque | Consulta de estoque |
| 8 | Produtos | Cadastro e edicao |
| 9 | Veiculos | Cadastro e listagem |
| 10 | OS | Fluxo completo da oficina |
| 11 | Movimentacoes | Entradas, saidas, ajustes e historico |
| 12 | Alertas | Minimo e zerado |
| 13 | Usuarios | Gestao de usuarios |
| 14 | UX | Refinamentos gerais |
| 15 | Testes | Unitarios e E2E |

## 21. MVP Web Minimo

Se for necessario reduzir o escopo para entregar uma primeira versao navegavel, a ordem minima deve ser:

1. Setup tecnico.
2. Atomic Design base.
3. Layout shell.
4. Cliente API.
5. Auth.
6. Permissoes.
7. Dashboard.
8. Estoque.
9. Veiculos.
10. Ordens de servico.

Produtos, movimentacoes, alertas e usuarios devem entrar logo depois, porque completam a operacao real do MVP.

## 22. Proximo Passo

O proximo passo pratico e implementar a **Fase 0 - Setup Tecnico Do Front-end** criando o projeto Nuxt na pasta:

```text
frontend/
```

Primeira entrega esperada:

- Nuxt rodando;
- Tailwind configurado;
- Shadcn Vue configurado;
- Pinia configurado;
- estrutura Atomic Design criada;
- pagina inicial temporaria;
- variavel `NUXT_PUBLIC_API_BASE_URL` configurada.

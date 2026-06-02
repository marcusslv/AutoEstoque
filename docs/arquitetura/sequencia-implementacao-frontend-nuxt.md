# Sequencia De Implementacao Do Front-end Nuxt

Este documento define a ordem recomendada para implementar o front-end web do AutoEstoque usando Nuxt, Vue, TypeScript, Tailwind CSS e Shadcn Vue.

O objetivo e construir o painel administrativo de forma incremental, conectando cada etapa ao backend ja existente e evitando criar telas desconectadas da API real.

## 1. Principios Da Sequencia

A implementacao deve seguir alguns principios:

- entregar fatias pequenas e testaveis;
- iniciar pela base tecnica;
- conectar cedo com a API real;
- proteger rotas desde o inicio;
- respeitar permissoes por perfil;
- priorizar fluxos centrais do MVP;
- evitar telas grandes antes da base de layout, auth e API estar pronta.

## 2. Dependencias De Backend

O front-end deve usar como contrato principal:

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

## 3. Fase 0 - Setup Tecnico Do Front-end

Objetivo:

Criar a base do projeto Nuxt e deixar a aplicacao rodando localmente.

Entregas:

- criar projeto Nuxt em `frontend/`;
- configurar TypeScript;
- configurar Tailwind CSS;
- configurar Shadcn Vue;
- configurar Pinia;
- configurar variavel `NUXT_PUBLIC_API_BASE_URL`;
- criar estrutura inicial de pastas;
- criar pagina inicial temporaria;
- validar servidor local.

Estrutura inicial esperada:

```text
frontend/
  app/
    components/
    layouts/
    middleware/
    modules/
    pages/
    plugins/
    shared/
  nuxt.config.ts
  package.json
```

Critérios de aceite:

- `npm install` executa com sucesso;
- `npm run dev` sobe o front-end;
- Tailwind funciona em uma pagina simples;
- Shadcn Vue possui ao menos um componente instalado;
- Pinia esta registrado.

## 4. Fase 1 - Base Visual E Layout

Objetivo:

Criar a estrutura visual principal do painel.

Entregas:

- layout publico;
- layout autenticado;
- sidebar;
- header;
- menu do usuario;
- container principal;
- componente de loading;
- componente de erro;
- componente de estado vazio;
- componente de acesso negado.

Rotas iniciais:

```text
/login
/dashboard
```

Componentes sugeridos:

```text
components/layout/AppShell.vue
components/layout/AppSidebar.vue
components/layout/AppHeader.vue
components/layout/UserMenu.vue
components/feedback/LoadingState.vue
components/feedback/ErrorState.vue
components/feedback/EmptyState.vue
components/feedback/ForbiddenState.vue
```

Critérios de aceite:

- layout autenticado exibe sidebar e header;
- layout publico nao exibe sidebar;
- interface funciona em desktop e mobile;
- menu principal ja considera placeholders das telas futuras.

## 5. Fase 2 - Cliente API E Tratamento De Erros

Objetivo:

Criar a camada central de comunicacao com o backend.

Entregas:

- `shared/api/apiClient.ts`;
- tratamento de `401`;
- tratamento de `403`;
- tratamento de `409`;
- tratamento de `422`;
- tratamento generico de `500`;
- tipos base de resposta;
- helper para mensagens de erro.

Arquivos sugeridos:

```text
shared/api/apiClient.ts
shared/api/apiErrors.ts
shared/api/apiTypes.ts
```

Critérios de aceite:

- cliente usa `NUXT_PUBLIC_API_BASE_URL`;
- cliente envia `Authorization: Bearer`;
- erro `401` limpa sessao;
- erro `422` pode ser mapeado para campos de formulario.

## 6. Fase 3 - Autenticacao

Objetivo:

Permitir login, logout e persistencia de sessao.

Entregas:

- tela de login;
- auth store com Pinia;
- persistencia do token;
- persistencia do usuario autenticado;
- logout integrado ao backend;
- middleware de rota autenticada;
- redirecionamento automatico.

Rotas de API:

```text
POST /api/v1/auth/login
POST /api/v1/auth/logout
```

Arquivos sugeridos:

```text
modules/auth/stores/authStore.ts
modules/auth/services/authApi.ts
modules/auth/components/LoginForm.vue
middleware/auth.ts
middleware/guest.ts
pages/login.vue
```

Critérios de aceite:

- usuario consegue fazer login com credenciais demo;
- token e usado nas chamadas autenticadas;
- logout revoga token;
- rota interna sem token redireciona para `/login`;
- usuario autenticado nao deve permanecer em `/login`.

## 7. Fase 4 - Permissoes Por Perfil

Objetivo:

Aplicar no front-end a mesma matriz de permissoes do backend.

Roles:

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
- acesso direto a rota proibida exibe tela 403 ou redireciona para uma tela permitida;
- backend continua retornando 403 se o usuario tentar forcar chamada proibida.

## 8. Fase 5 - Dashboard

Objetivo:

Criar a primeira tela autenticada de valor gerencial.

Rotas de API:

```text
GET /api/v1/dashboard
GET /api/v1/dashboard/most-consumed-products
```

Entregas:

- cards de indicadores;
- lista de movimentacoes recentes;
- ranking de produtos mais consumidos;
- filtro por data ou periodo;
- estado vazio;
- estado de erro.

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
- loading e erro sao tratados.

## 9. Fase 6 - Consulta De Estoque

Objetivo:

Permitir que todos os perfis operacionais consultem estoque.

Rota de API:

```text
GET /api/v1/stock
```

Entregas:

- tela `/stock`;
- busca por produto;
- tabela de produtos;
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

- `mechanic` consegue acessar estoque;
- busca funciona;
- produtos sem saldo aparecem como zero;
- status e quantidade ficam claros na tabela.

## 10. Fase 7 - Produtos

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
- validacao basica no front;
- tratamento de erros `422`;
- restricao por perfil.

Arquivos sugeridos:

```text
modules/catalog/components/ProductForm.vue
modules/catalog/components/ProductTable.vue
modules/catalog/composables/useProducts.ts
pages/products/index.vue
```

Critérios de aceite:

- `admin` cria produto;
- `admin` edita produto;
- `mechanic` nao acessa tela de produtos;
- erros de SKU/codigo de barras duplicados sao exibidos.

## 11. Fase 8 - Movimentacoes De Estoque

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

## 12. Fase 9 - Alertas De Estoque

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

Critérios de aceite:

- alertas abaixo do minimo aparecem;
- alertas zerados aparecem;
- `mechanic` nao acessa a tela;
- estado vazio e tratado.

## 13. Fase 10 - Veiculos

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
- tratamento de placa duplicada.

Arquivos sugeridos:

```text
modules/workshop/services/workshopApi.ts
modules/workshop/components/VehicleForm.vue
modules/workshop/components/VehicleTable.vue
modules/workshop/composables/useVehicles.ts
pages/vehicles/index.vue
```

Critérios de aceite:

- todos os perfis operacionais acessam veiculos;
- cadastro de veiculo funciona;
- busca por placa, modelo ou proprietario funciona;
- erro de placa duplicada e exibido.

## 14. Fase 11 - Ordens De Servico

Objetivo:

Implementar fluxo operacional principal da oficina.

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
modules/workshop/components/ServiceOrderDetails.vue
modules/workshop/components/AddPartDialog.vue
modules/workshop/components/FinishServiceOrderDialog.vue
modules/workshop/composables/useServiceOrders.ts
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

## 15. Fase 12 - Usuarios

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

Critérios de aceite:

- `owner`, `manager` e `admin` acessam;
- `mechanic` nao acessa;
- limite de usuarios retorna mensagem clara;
- usuario inativado aparece com status correto.

## 16. Fase 13 - Refinamento De UX

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
- formatacao de data.

Critérios de aceite:

- telas principais funcionam em desktop e mobile;
- erros sao compreensiveis;
- acoes destrutivas pedem confirmacao;
- usuario sempre entende o resultado de uma acao.

## 17. Fase 14 - Testes Do Front-end

Objetivo:

Adicionar testes onde houver maior risco de regressao.

Entregas:

- testes de auth store;
- testes de permissoes;
- testes de componentes criticos;
- testes de services com mocks;
- teste E2E do fluxo de login;
- teste E2E de criar OS e finalizar.

Ferramentas sugeridas:

- Vitest;
- Vue Test Utils;
- Playwright.

Critérios de aceite:

- login testado;
- permissao por perfil testada;
- fluxo de OS testado;
- pipeline consegue rodar testes.

## 18. Ordem Resumida

| Ordem | Fase | Entrega |
| --- | --- | --- |
| 0 | Setup tecnico | Nuxt, Tailwind, Shadcn, Pinia |
| 1 | Layout | Shell autenticado e publico |
| 2 | API | Cliente HTTP e erros |
| 3 | Auth | Login, logout e sessao |
| 4 | Permissoes | Rotas e menus por perfil |
| 5 | Dashboard | Indicadores gerenciais |
| 6 | Estoque | Consulta de estoque |
| 7 | Produtos | Cadastro e edicao |
| 8 | Movimentacoes | Entradas, saidas, ajustes e historico |
| 9 | Alertas | Minimo e zerado |
| 10 | Veiculos | Cadastro e listagem |
| 11 | OS | Fluxo completo da oficina |
| 12 | Usuarios | Gestao de usuarios |
| 13 | UX | Refinamentos gerais |
| 14 | Testes | Unitarios e E2E |

## 19. MVP Web Minimo

Se for necessario reduzir o escopo para entregar uma primeira versao navegavel, a ordem minima deve ser:

1. Setup tecnico.
2. Layout.
3. Cliente API.
4. Auth.
5. Permissoes.
6. Dashboard.
7. Estoque.
8. Produtos.
9. Veiculos.
10. Ordens de servico.

Movimentacoes manuais, alertas e usuarios podem entrar logo depois, mas o ideal e nao adiar por muito tempo porque fazem parte do valor central do produto.

## 20. Proximo Passo

O proximo passo pratico e implementar a **Fase 0 - Setup Tecnico Do Front-end** criando o projeto Nuxt na pasta:

```text
frontend/
```

# AutoEstoque Front-end - Arquitetura Utilizada

## 1. Objetivo

Este documento descreve a arquitetura utilizada no front-end do AutoEstoque.

O front-end foi estruturado para ser um painel administrativo SaaS, com foco em operação diária de oficinas mecânicas. A arquitetura prioriza organização por domínio, reutilização de interface com Atomic Design, integração clara com a API do backend e separação entre componentes visuais, regras de apresentação e comunicação HTTP.

## 2. Stack Utilizada

- Nuxt 4
- Vue 3
- TypeScript
- Tailwind CSS
- Pinia
- Fetch/ofetch via cliente HTTP compartilhado
- Vitest
- Vue Test Utils
- Playwright

## 3. Estilo Arquitetural

O front-end utiliza uma combinação de:

- Nuxt como base da aplicação.
- Atomic Design para organização dos componentes visuais reutilizáveis.
- Organização por módulos de domínio para funcionalidades de negócio.
- API First para comunicação com o backend.
- Controle de autenticação e permissões por perfil.
- Camada compartilhada para cliente HTTP, erros, sessão, permissões e utilitários.

A ideia central é manter as páginas simples, delegando regras de tela para composables, chamadas HTTP para services e elementos visuais reutilizáveis para os componentes de UI.

## 4. Estrutura Geral

Estrutura principal utilizada dentro de `frontend/app`:

```text
app
├── assets
├── components
│   ├── auth
│   ├── feedback
│   ├── layout
│   │   ├── shell
│   │   └── templates
│   └── ui
│       ├── atoms
│       ├── molecules
│       └── organisms
├── layouts
├── middleware
├── modules
│   ├── auth
│   ├── catalog
│   ├── dashboard
│   ├── inventory
│   ├── settings
│   ├── users
│   └── workshop
├── pages
├── plugins
└── shared
    ├── api
    ├── auth
    ├── errors
    ├── feedback
    ├── permissions
    └── utils
```

## 5. Atomic Design

O Atomic Design é usado para organizar os componentes reutilizáveis da interface.

### 5.1 Atoms

Componentes pequenos, genéricos e sem regra de negócio.

Responsabilidades:

- Exibir elementos básicos de interface.
- Receber dados por props.
- Emitir eventos simples.
- Não acessar API.
- Não conhecer módulos de negócio.

Exemplos:

- Botões.
- Inputs.
- Selects.
- Badges básicos.
- Estados visuais pequenos.

Pasta:

```text
app/components/ui/atoms
```

### 5.2 Molecules

Composições pequenas de atoms.

Responsabilidades:

- Agrupar campos, labels, mensagens e pequenas interações.
- Continuar sem acesso direto à API.
- Ser reutilizáveis em múltiplas telas.

Exemplos:

- Campo de formulário com label e erro.
- Campo de busca.
- Indicador de status.
- Card de métrica.

Pasta:

```text
app/components/ui/molecules
```

### 5.3 Organisms

Blocos maiores de interface, ainda reutilizáveis, mas mais próximos da experiência de tela.

Responsabilidades:

- Montar tabelas, formulários, cabeçalhos, filtros e diálogos.
- Receber dados e callbacks por props.
- Não concentrar regra de negócio complexa.
- Não chamar API diretamente.

Exemplos:

- Tabela de dados.
- Formulário em diálogo.
- Cabeçalho de página.
- Barra de filtros.
- Estados vazios e de erro.

Pasta:

```text
app/components/ui/organisms
```

### 5.4 Templates

Estruturas de tela reutilizáveis.

Responsabilidades:

- Definir composição visual de páginas.
- Organizar header, conteúdo, filtros, ações e estados.
- Permitir que páginas diferentes compartilhem o mesmo padrão.

Exemplos:

- Template de listagem.
- Template de dashboard.
- Template de detalhe.
- Template público de autenticação.

Pasta:

```text
app/components/layout/templates
```

## 6. Módulos de Domínio

As funcionalidades de negócio ficam organizadas por domínio dentro de `app/modules`.

Cada módulo pode conter:

```text
modules/{dominio}
├── components
├── composables
├── services
└── types
```

### 6.1 Components

Componentes específicos daquele domínio.

Exemplos:

- Formulário de produto.
- Formulário de veículo.
- Lista de peças da ordem de serviço.
- Formulário de configurações da oficina.

### 6.2 Composables

Concentram estado e regras de apresentação da tela.

Responsabilidades:

- Controlar loading.
- Controlar erro.
- Controlar filtros.
- Controlar paginação.
- Orquestrar chamadas para services.
- Preparar dados para os componentes.

### 6.3 Services

Responsáveis por comunicação com a API.

Responsabilidades:

- Chamar endpoints HTTP.
- Enviar payloads no contrato esperado pelo backend.
- Converter respostas externas para tipos internos quando necessário.
- Isolar detalhes de URL, método HTTP e serialização.

### 6.4 Types

Definem os tipos TypeScript utilizados pelo módulo.

Responsabilidades:

- Tipos de entidades exibidas no front-end.
- Tipos de filtros.
- Tipos de payloads de criação e edição.
- Tipos de respostas da API.

## 7. Módulos Existentes

### Auth

Responsável por login, sessão autenticada, usuário atual e encerramento de sessão.

Pasta:

```text
app/modules/auth
```

### Dashboard

Responsável pela visão gerencial inicial da oficina.

Inclui indicadores como:

- Total de produtos.
- Produtos abaixo do mínimo.
- Valor total em estoque.
- Produtos mais consumidos.
- Movimentações recentes.

Pasta:

```text
app/modules/dashboard
```

### Catalog

Responsável por produtos e consulta de estoque.

Inclui:

- Cadastro de produtos.
- Edição de produtos.
- Consulta de estoque.
- Visualização de status de estoque.

Pasta:

```text
app/modules/catalog
```

### Inventory

Responsável pelas movimentações e alertas de estoque.

Inclui:

- Entrada de estoque.
- Saída de estoque.
- Histórico de movimentações.
- Alertas de estoque mínimo e estoque zerado.

Pasta:

```text
app/modules/inventory
```

### Workshop

Responsável pela operação da oficina.

Inclui:

- Veículos.
- Ordens de serviço.
- Detalhe de ordem de serviço.
- Peças utilizadas em serviços.

Pasta:

```text
app/modules/workshop
```

### Users

Responsável pela gestão de usuários.

Inclui:

- Listagem de usuários.
- Cadastro de usuários.
- Edição de usuários.
- Controle de perfil.

Pasta:

```text
app/modules/users
```

### Settings

Responsável pelas configurações da oficina.

Inclui:

- Dados gerais da oficina.
- Preferências de estoque.
- Preferências de alertas.
- Preferências operacionais.

Pasta:

```text
app/modules/settings
```

## 8. Pages

As páginas ficam em `app/pages` e devem ser finas.

Responsabilidades das pages:

- Definir rota da tela.
- Definir metadata da página.
- Aplicar middleware quando necessário.
- Conectar templates, componentes e composables.
- Não concentrar regra de negócio.
- Não chamar API diretamente quando existir service para isso.

Exemplo de responsabilidades esperadas:

```text
page -> composable -> service -> apiClient -> backend
```

## 9. Layouts e Shell

Os layouts controlam a estrutura geral da aplicação.

Layouts principais:

- Layout autenticado.
- Layout público.
- Layout padrão.

O shell autenticado centraliza:

- Menu lateral.
- Cabeçalho.
- Informações do usuário.
- Ações de navegação.
- Links condicionados por permissão.

Pastas relacionadas:

```text
app/layouts
app/components/layout/shell
```

## 10. Cliente de API

A comunicação HTTP é centralizada em uma camada compartilhada.

Pasta:

```text
app/shared/api
```

Responsabilidades:

- Definir base URL da API.
- Anexar token Bearer quando existir sessão autenticada.
- Padronizar erros HTTP.
- Evitar duplicação de configuração HTTP nos módulos.
- Servir como ponto único para evoluções de autenticação, headers e tratamento de erro.

A URL da API deve ser configurada via variável pública do Nuxt:

```text
NUXT_PUBLIC_API_BASE_URL
```

## 11. Contratos com o Backend

O front-end deve seguir o contrato documentado pelo backend em:

```text
backend/docs/openapi.yaml
```

Convenções:

- O contrato HTTP segue o backend.
- Payloads enviados para API podem usar o formato esperado pelo backend.
- O front-end pode mapear respostas para tipos internos mais ergonômicos.
- Services são o ponto preferencial para adaptar nomes de campos e formatos.
- Componentes não devem depender diretamente de detalhes do contrato HTTP.

## 12. Autenticação

A autenticação é baseada em token.

Responsabilidades principais:

- Login via API.
- Persistência do token.
- Persistência dos dados do usuário autenticado.
- Envio automático do token nas requisições.
- Logout com revogação do token no backend quando disponível.
- Redirecionamento de rotas protegidas.

Elementos relacionados:

```text
app/modules/auth
app/shared/auth
app/middleware/auth.ts
app/middleware/guest.ts
```

## 13. Permissões por Perfil

O controle de acesso por perfil é aplicado no front-end para melhorar a experiência e esconder ações indisponíveis.

Perfis considerados:

- Proprietário.
- Gerente.
- Administrador.
- Mecânico.

A camada de permissões fica em:

```text
app/shared/permissions
```

Importante:

- A permissão no front-end não substitui a validação no backend.
- O backend continua sendo a fonte final de autorização.
- O front-end apenas adapta navegação, botões e telas conforme o perfil.

## 14. Tratamento de Erros e Feedback

O tratamento de erros deve ser consistente em todas as telas.

Elementos utilizados:

- Estados de loading.
- Estados vazios.
- Estados de erro.
- Estados de acesso negado.
- Confirmações.
- Toasts.

Pastas relacionadas:

```text
app/components/feedback
app/shared/errors
app/shared/feedback
```

Regras:

- Erros técnicos devem ser convertidos para mensagens compreensíveis.
- Componentes de tela devem receber estados prontos.
- Services não devem exibir toast diretamente.
- Composables ou pages coordenam mensagens de sucesso e erro.

## 15. Estilo Visual

O front-end segue uma estética operacional, adequada para uso diário em oficina.

Diretrizes:

- Interface objetiva e escaneável.
- Menus previsíveis.
- Tabelas e filtros para consulta rápida.
- Componentes consistentes.
- Baixo ruído visual.
- Layout responsivo.
- Uso controlado de cards.
- Hierarquia clara de informação.

## 16. Testes

O front-end utiliza testes unitários e end-to-end.

Ferramentas:

- Vitest para testes unitários.
- Vue Test Utils para componentes e composables.
- Playwright para fluxos de interface.

Comandos principais:

```bash
pnpm test
pnpm test:unit
pnpm test:e2e
pnpm exec vue-tsc --noEmit
pnpm build
```

Diretrizes:

- Services devem ter testes para contrato e mapeamento de dados.
- Composables devem ter testes quando concentrarem regra relevante.
- Fluxos críticos devem ser cobertos por testes e2e.
- Componentes visuais muito simples não precisam de testes isolados obrigatórios.

## 17. Convenções de Implementação

### Pages

- Devem ser simples.
- Devem usar templates e componentes de domínio.
- Devem delegar regra para composables.

### Components

- Componentes de UI não acessam API.
- Componentes de domínio recebem dados e eventos.
- Componentes devem ser reutilizáveis dentro do limite natural do domínio.

### Composables

- Controlam estado de tela.
- Orquestram services.
- Expõem dados já preparados para a page.
- Centralizam ações como carregar, salvar, atualizar e remover.

### Services

- Chamam API.
- Conhecem endpoints.
- Adaptam payloads e respostas.
- Não manipulam estado visual.

### Types

- Devem ficar próximos do módulo que os utiliza.
- Tipos compartilhados só devem ir para `shared` quando forem realmente reutilizados por múltiplos módulos.

## 18. Fluxo Recomendado Para Nova Funcionalidade

Ao implementar uma nova funcionalidade:

1. Identificar o módulo de domínio.
2. Criar ou atualizar os tipos em `types`.
3. Criar ou atualizar o service da API.
4. Criar ou atualizar composable de estado.
5. Criar componentes específicos do módulo.
6. Criar ou atualizar a page.
7. Aplicar permissões quando necessário.
8. Adicionar feedback de loading, erro e vazio.
9. Criar testes unitários para regras e services.
10. Validar contrato com `backend/docs/openapi.yaml`.

## 19. Exemplo de Fluxo Interno

Exemplo para uma tela de listagem:

```text
pages/products.vue
  -> useProducts()
    -> productsApi.list()
      -> apiClient()
        -> Backend API
```

Exemplo para uma ação de cadastro:

```text
form component
  -> emit submit
    -> page/composable
      -> service.create(payload)
        -> apiClient
          -> backend
```

## 20. Decisões Arquiteturais

- O front-end não implementa Clean Architecture formal como o backend.
- A separação principal é feita por módulos, services, composables e componentes.
- A regra de negócio de domínio permanece no backend.
- O front-end concentra regra de apresentação, estado de tela, permissões visuais e adaptação de contrato.
- O Atomic Design é usado para reduzir duplicação visual e manter consistência.
- A comunicação com API é centralizada para facilitar manutenção.

## 21. Critério de Qualidade

Uma nova tela ou funcionalidade deve ser considerada bem implementada quando:

- Segue o contrato da API.
- Usa o módulo correto.
- Não duplica lógica HTTP.
- Possui loading, erro e estado vazio quando aplicável.
- Respeita permissões do usuário.
- Mantém a page simples.
- Reutiliza componentes de UI existentes.
- Possui testes proporcionais ao risco da alteração.


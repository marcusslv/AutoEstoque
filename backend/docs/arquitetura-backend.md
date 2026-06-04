# AutoEstoque Backend - Arquitetura

## Objetivo

Este documento descreve a arquitetura utilizada no backend do AutoEstoque.

O backend foi construido para ser uma API SaaS multiempresa para oficinas mecanicas, mantendo separacao clara entre regras de negocio, casos de uso, infraestrutura Laravel e interface HTTP.

## Estilo Arquitetural

A arquitetura utilizada combina:

- Modular Monolith.
- Clean Architecture por modulo.
- Domain-Driven Design tatico.
- API First.
- Multi-Tenant por tenant resolvido via autenticacao.
- DTOs de Input e Output nos use cases.
- Presenters para formatar respostas HTTP.
- Repositories e Queries como contratos de acesso a dados.

## Por Que Modular Monolith

O AutoEstoque ainda esta em fase de MVP/produto inicial. Nesse contexto, um monolito modular e mais adequado que microservicos porque:

- reduz complexidade operacional;
- facilita transacoes locais;
- acelera desenvolvimento;
- evita distribuicao prematura;
- permite evoluir cada modulo separadamente dentro do mesmo deploy;
- preserva a possibilidade de extrair servicos no futuro, se houver necessidade real.

## Organizacao Geral

O codigo principal fica em:

```text
app/Modules
```

Cada modulo pode conter as camadas:

```text
Application
Domain
Infrastructure
Interfaces
```

Tambem existem modulos compartilhados:

```text
Shared
Tenant
```

## Modulos Do Backend

### Identity

Responsavel por autenticacao, usuarios, recuperacao de senha, logout e permissao por perfil.

Principais responsabilidades:

- autenticar usuario;
- emitir token;
- revogar token;
- recuperar senha;
- gerenciar usuarios da oficina;
- bloquear usuario inativo;
- resolver usuario autenticado;
- aplicar autorizacao por perfil.

### Tenant

Responsavel pela resolucao do tenant atual.

Principais responsabilidades:

- representar o tenant/oficina;
- validar `TenantId`;
- manter contexto do tenant autenticado;
- impedir acesso cruzado entre oficinas.

### Settings

Responsavel pelas configuracoes da oficina.

Principais responsabilidades:

- consultar configuracoes da oficina;
- criar configuracoes padrao quando ainda nao existem;
- atualizar dados cadastrais, operacionais e notificacoes;
- manter configuracoes sempre vinculadas ao tenant atual.

### Catalog

Responsavel pelo cadastro de produtos/pecas.

Principais responsabilidades:

- cadastrar produto;
- editar produto;
- validar SKU e codigo de barras;
- consultar produtos com informacoes de estoque;
- manter dados cadastrais separados das movimentacoes de estoque.

### Inventory

Responsavel por saldo, movimentacoes e alertas.

Principais responsabilidades:

- registrar entrada de estoque;
- registrar saida de estoque;
- registrar ajuste manual;
- consultar historico de movimentacoes;
- gerar alerta de estoque minimo;
- gerar alerta de estoque zerado;
- controlar saldo por produto e tenant.

### Workshop

Responsavel por veiculos e ordens de servico.

Principais responsabilidades:

- cadastrar veiculo;
- listar veiculos;
- criar ordem de servico;
- listar ordens de servico;
- detalhar ordem de servico;
- adicionar pecas a OS;
- finalizar OS com baixa automatica;
- manter vinculo formal entre OS e movimentacoes de estoque.

### Dashboard

Responsavel por consultas agregadas e indicadores.

Principais responsabilidades:

- visualizar indicadores do dashboard;
- listar produtos mais consumidos;
- consultar dados agregados sem concentrar regras centrais de dominio.

### Shared

Responsavel por contratos e objetos comuns.

Principais responsabilidades:

- contratos `UseCase`, `InputDto` e `OutputDto`;
- contrato `TransactionManager`;
- entidade abstrata de dominio;
- notification pattern;
- excecao de validacao de dominio;
- presenter JSON compartilhado.

## Camadas

## Domain

Camada que concentra regras de negocio puras.

Contem:

- entidades;
- value objects;
- validators;
- factories;
- repositories como contratos;
- excecoes de dominio;
- notification pattern.

Regra importante:

```text
Domain nao deve depender de Laravel, Eloquent, HTTP, Request, Response ou banco.
```

## Application

Camada dos casos de uso.

Contem:

- use cases;
- DTOs de input;
- DTOs de output;
- contracts de queries/repositories quando o caso de uso precisa de uma abstracao especifica;
- orquestracao de fluxos;
- chamadas ao dominio e aos repositories.

Regra importante:

```text
Application nao deve receber Request HTTP nem retornar Response HTTP.
```

## Infrastructure

Camada de implementacao tecnica.

Contem:

- models Eloquent;
- repositories Eloquent;
- queries Eloquent;
- implementacoes de token;
- implementacoes de password reset;
- persistencia e integracoes.

Regra importante:

```text
Infrastructure pode depender de Laravel, mas deve implementar contratos consumidos pelas camadas internas.
```

## Interfaces

Camada de entrada e saida.

Contem:

- controllers;
- form requests;
- presenters;
- middleware HTTP;
- conversao de Request para Input DTO;
- conversao de Output DTO para JSON.

Regra importante:

```text
Interfaces conhece HTTP. Application e Domain nao conhecem HTTP.
```

## Fluxo Padrao De Uma Requisicao

Fluxo tipico:

```text
HTTP Request
  -> Middleware de autenticacao/autorizacao
  -> FormRequest
  -> Controller
  -> Input DTO
  -> UseCase
  -> Domain/Repository/Query
  -> Output DTO
  -> Presenter
  -> JSON Response
```

Exemplo conceitual:

```text
PATCH /api/v1/settings/workshop
  -> UpdateWorkshopSettingsRequest
  -> UpdateWorkshopSettingsController
  -> UpdateWorkshopSettingsInput
  -> UpdateWorkshopSettingsUseCase
  -> WorkshopSettingsRepository
  -> WorkshopSettingsOutput
  -> WorkshopSettingsPresenter
```

## DTOs De Input E Output

Todo caso de uso deve receber um DTO de entrada e retornar um DTO de saida.

O Input representa a intencao de executar o caso de uso.

Exemplo:

```text
UpdateWorkshopSettingsInput
```

O Output representa o resultado do caso de uso.

Exemplo:

```text
WorkshopSettingsOutput
```

Beneficios:

- evita passar `Request` para Application;
- evita retornar Model Eloquent para Interfaces;
- facilita testes;
- explicita o contrato do caso de uso;
- reduz acoplamento com Laravel.

## Presenters

Presenters sao usados para transformar Output DTO em resposta HTTP.

Responsabilidades:

- montar estrutura `data`;
- montar estrutura `meta`, quando necessario;
- definir formato externo snake_case;
- padronizar resposta JSON;
- manter controller fino.

Exemplos:

- `WorkshopSettingsPresenter`;
- `CreateProductPresenter`;
- `ListStockPresenter`;
- `ViewDashboardPresenter`;
- `ShowServiceOrderPresenter`.

## Repositories E Queries

O backend usa dois estilos de acesso a dados:

### Repositories

Usados quando existe operacao de dominio ou persistencia de aggregate/entity.

Exemplos:

- `ProductRepository`;
- `InventoryItemRepository`;
- `StockMovementRepository`;
- `VehicleRepository`;
- `ServiceOrderRepository`;
- `WorkshopSettingsRepository`.

### Queries

Usadas para consultas agregadas, relatorios e listagens otimizadas.

Exemplos:

- `DashboardQuery`;
- `MostConsumedProductsQuery`;
- `StockMovementHistoryQuery`;
- `VehicleListQuery`;
- `ServiceOrderListQuery`;
- `ServiceOrderDetailsQuery`.

## Entidades, Validators, Factories E Notification Pattern

Entidades de dominio devem representar estado valido.

Padrao utilizado:

- Value Objects validam regras simples de valor.
- Factories criam entidades e normalizam dados de entrada.
- Validators validam invariantes da entidade.
- Entidades possuem uma `Notification`.
- Se houver erro na notification, a entidade dispara `DomainValidationException`.

Fluxo:

```text
Factory
  -> Entity
  -> Validator
  -> Notification
  -> DomainValidationException, se houver erro
```

Exemplos:

- `ProductFactory`, `ProductValidator`, `Product`;
- `InventoryItemFactory`, `InventoryItemValidator`, `InventoryItem`;
- `VehicleFactory`, `VehicleValidator`, `Vehicle`;
- `ServiceOrderFactory`, `ServiceOrderValidator`, `ServiceOrder`;
- `WorkshopSettingsFactory`, `WorkshopSettingsValidator`, `WorkshopSettings`.

## Multi-Tenant

O backend e multiempresa.

Regras:

- cada registro operacional possui `tenant_id`;
- o tenant atual e resolvido pelo token autenticado;
- controllers recebem `TenantContext`;
- use cases recebem `tenantId` no Input DTO;
- repositories e queries filtram por tenant;
- dados de uma oficina nao devem aparecer para outra oficina.

## Autenticacao E Autorizacao

Autenticacao:

- login em `/api/v1/auth/login`;
- token Bearer emitido para o usuario;
- logout revoga token atual;
- tenant e usuario autenticado sao resolvidos a partir do token.

Autorizacao:

- middleware `auth.api`;
- middleware `role`;
- perfis principais:
  - `owner`;
  - `manager`;
  - `admin`;
  - `mechanic`.

Perfis administrativos:

```text
owner, manager, admin
```

Perfis de operacao da oficina:

```text
owner, manager, admin, mechanic
```

## Transacoes

Fluxos com multiplas escritas devem usar transacao.

Exemplo principal:

```text
Finalizar OS com baixa automatica
```

Esse fluxo precisa:

- validar OS;
- validar pecas;
- registrar movimentacoes de saida;
- atualizar saldo;
- finalizar OS;
- criar vinculos entre OS e movimentacoes.

## API First

A API e documentada em:

```text
backend/docs/openapi.yaml
```

O contrato externo usa:

- rotas REST em `/api/v1`;
- request/response JSON;
- campos externos em snake_case;
- autenticação Bearer;
- respostas com `data`;
- respostas com `meta` em listagens/consultas agregadas.

## Testes

O backend possui testes de feature e testes unitarios.

Padrao:

- feature tests validam endpoint, permissao, tenant, payload e persistencia;
- unit tests validam use cases e regras isoladas quando necessario;
- `RefreshDatabase` e usado nos testes HTTP.

Comando principal:

```bash
php artisan test
```

## Decisoes Importantes

- O saldo de estoque pertence ao modulo Inventory, nao ao cadastro de produto.
- Movimentacoes registram usuario, tenant, produto, motivo, tipo, quantidade e data.
- Dashboard e consultas agregadas nao devem virar donos das regras de estoque.
- OS finalizada gera baixa automatica e vinculo formal com movimentacoes.
- Configuracoes da oficina pertencem ao tenant e podem influenciar acoes futuras.
- Dados de plano podem ser consultados, mas nao devem ser alterados diretamente pela tela de configuracoes.


# AutoEstoque Backend

Backend da plataforma AutoEstoque, uma API para controle de estoque de oficinas mecanicas.

## Stack

- Laravel 13.12
- PHP 8.4
- PostgreSQL
- Redis
- Docker Compose
- Nginx

## Requisitos

- Docker
- Docker Compose

Nao e necessario instalar PHP, Composer, PostgreSQL ou Redis diretamente na maquina para rodar o backend localmente.

## Estrutura

Este backend fica dentro da pasta `backend/`, mas os containers Docker sao orquestrados pelo `docker-compose.yml` na raiz do repositorio.

Estrutura principal:

```text
AutoEstoque/
  backend/
    app/
    config/
    database/
    public/
    routes/
    artisan
    composer.json
  docker/
  docker-compose.yml
```

## Subir O Ambiente

Execute os comandos a partir da raiz do projeto:

```bash
docker compose up -d
```

A aplicacao ficara disponivel em:

```text
http://localhost:8080
```

## Variaveis De Ambiente

O arquivo `.env` ja esta configurado para o ambiente Docker local.

Principais valores:

```env
APP_NAME=AutoEstoque
APP_URL=http://localhost:8080

DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=autoestoque
DB_USERNAME=autoestoque
DB_PASSWORD=secret

CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

REDIS_HOST=redis
REDIS_PORT=6379
```

## Comandos Uteis

Todos os comandos abaixo devem ser executados a partir da raiz do projeto.

Instalar dependencias:

```bash
docker compose exec backend composer install
```

Rodar migrations:

```bash
docker compose exec backend php artisan migrate
```

Verificar status das migrations:

```bash
docker compose exec backend php artisan migrate:status
```

Rodar testes:

```bash
docker compose exec backend php artisan test
```

Executar Pint:

```bash
docker compose exec backend ./vendor/bin/pint
```

Abrir Tinker:

```bash
docker compose exec backend php artisan tinker
```

Verificar versao do Laravel:

```bash
docker compose exec backend php artisan --version
```

## Queue E Scheduler

Os workers ficam em um profile separado.

Subir backend com queue e scheduler:

```bash
docker compose --profile workers up -d
```

Subir apenas os servicos principais:

```bash
docker compose up -d
```

## Banco De Dados

Acessar PostgreSQL:

```bash
docker compose exec postgres psql -U autoestoque -d autoestoque
```

Apagar containers mantendo volumes:

```bash
docker compose down
```

Apagar containers e volumes locais:

```bash
docker compose down -v
```

Use `down -v` apenas quando quiser remover os dados locais.

## Arquitetura

O backend sera organizado como um monolito modular usando Clean Architecture e DDD.

Modulos planejados:

- `Identity`
- `Tenant`
- `Shared`
- `Catalog`
- `Inventory`
- `Workshop`
- `Dashboard`

Estrutura alvo:

```text
app/
  Modules/
    Shared/
      Application/
      Interfaces/
    Tenant/
      Domain/
      Application/
      Infrastructure/
      Interfaces/
    Catalog/
      Domain/
      Application/
      Infrastructure/
      Interfaces/
    Inventory/
    Identity/
    Workshop/
    Dashboard/
```

Convencao para casos de uso:

```text
Application/
  UseCases/
    NomeDoCaso/
      Contracts/
        AlgumaPorta.php
      Dtos/
        NomeDoCasoInput.php
        NomeDoCasoOutput.php
      NomeDoCasoUseCase.php
```

- `Dtos`: entradas e saidas do caso de uso.
- `Contracts`: portas especificas da aplicacao, como queries, gateways ou services externos.
- `NomeDoCasoUseCase.php`: orquestra a regra de aplicacao e depende de contratos, repositorios e factories.

Mais detalhes estao na documentacao da raiz:

```text
docs/arquitetura/analise-clean-architecture-ddd.md
docs/arquitetura/setup-backend-docker.md
docs/arquitetura/sequencia-implementacao-modulos-use-cases.md
```

## API Inicial

Rotas disponiveis na fundacao tecnica:

```text
GET /api/v1/health
GET /api/v1/context/tenant
GET /api/v1/dashboard
GET /api/v1/stock
GET /api/v1/inventory/alerts/minimum-stock
GET /api/v1/inventory/alerts/zero-stock
GET /api/v1/inventory/movements
POST /api/v1/inventory/adjustments
POST /api/v1/inventory/entries
POST /api/v1/inventory/outputs
POST /api/v1/products
PATCH /api/v1/products/{product}
```

### Visualizar Dashboard

```http
GET /api/v1/dashboard
X-Tenant-Id: {tenant_id}
```

Filtros aceitos:

- `date`: data base para movimentacoes do dia. Quando omitido, usa a data atual.
- `recent_movements_limit`: entre `1` e `20`.

Indicadores retornados:

- total de produtos.
- produtos abaixo do minimo.
- produtos zerados.
- valor total em estoque.
- movimentacoes do dia.
- ultimas movimentacoes do dia.

### Gerar Alertas De Estoque Minimo

```http
GET /api/v1/inventory/alerts/minimum-stock
X-Tenant-Id: {tenant_id}
```

Filtros aceitos:

- `limit`: entre `1` e `100`

Nesta versao, os alertas sao calculados sob demanda a partir de `products.minimum_stock` e `inventory_items.current_stock`. Produtos com saldo `0` ficam reservados para o alerta de estoque zerado.

### Gerar Alertas De Estoque Zerado

```http
GET /api/v1/inventory/alerts/zero-stock
X-Tenant-Id: {tenant_id}
```

Filtros aceitos:

- `limit`: entre `1` e `100`

Nesta versao, os alertas sao calculados sob demanda. Produtos sem movimentacao tambem aparecem como estoque zerado, pois o saldo considerado e `0`.

### Consultar Historico De Movimentacoes

```http
GET /api/v1/inventory/movements
X-Tenant-Id: {tenant_id}
```

Filtros aceitos:

- `product_id`
- `direction`: `entry` ou `output`
- `type`: `purchase`, `return`, `service_consumption`, `loss`, `breakage` ou `manual_adjustment`
- `user_id`
- `occurred_from`
- `occurred_to`
- `limit`: entre `1` e `100`

Exemplo:

```http
GET /api/v1/inventory/movements?direction=output&type=service_consumption&limit=20
X-Tenant-Id: {tenant_id}
```

### Registrar Ajuste Manual De Estoque

```http
POST /api/v1/inventory/adjustments
X-Tenant-Id: {tenant_id}
X-User-Id: {user_id}
Content-Type: application/json
```

```json
{
  "product_id": "uuid-do-produto",
  "direction": "entry",
  "quantity": 4,
  "reason": "Conferencia de estoque",
  "note": "Inventario semanal"
}
```

Direcoes aceitas:

- `entry`
- `output`

O ajuste manual sempre registra o movimento com `type` igual a `manual_adjustment`. Quando a direcao for `output` e o estoque for insuficiente, a API retorna `409 Conflict`.

### Registrar Entrada De Estoque

```http
POST /api/v1/inventory/entries
X-Tenant-Id: {tenant_id}
X-User-Id: {user_id}
Content-Type: application/json
```

```json
{
  "product_id": "uuid-do-produto",
  "type": "purchase",
  "quantity": 5,
  "reason": "Compra de reposicao",
  "note": "Nota 123",
  "unit_cost_in_cents": 2590
}
```

Tipos de entrada aceitos:

- `purchase`
- `manual_adjustment`
- `return`

### Registrar Saida De Estoque

```http
POST /api/v1/inventory/outputs
X-Tenant-Id: {tenant_id}
X-User-Id: {user_id}
Content-Type: application/json
```

```json
{
  "product_id": "uuid-do-produto",
  "type": "service_consumption",
  "quantity": 2,
  "reason": "Consumo em servico",
  "note": "OS 123"
}
```

Tipos de saida aceitos:

- `service_consumption`
- `loss`
- `breakage`
- `manual_adjustment`

Quando o estoque for insuficiente, a API retorna `409 Conflict`.

A rota `/api/v1/context/tenant` valida o tenant temporario usando o header:

```http
X-Tenant-Id: 018f95f2-0f08-7f85-9b31-2d833a1a2f41
```

Esse header sera usado enquanto o fluxo completo de autenticacao e multiempresa ainda nao estiver implementado.

Consultar estoque:

```http
GET /api/v1/stock?search=filtro
X-Tenant-Id: 018f95f2-0f08-7f85-9b31-2d833a1a2f42
```

A consulta retorna produtos cadastrados no tenant atual. Produtos sem movimentacao retornam `current_stock` igual a `0` e `stock_status` igual a `zero`. Produtos com entradas registradas retornam `current_stock` real a partir do modulo `Inventory`.

Registrar entrada de estoque:

```http
POST /api/v1/inventory/entries
X-Tenant-Id: 018f95f2-0f08-7f85-9b31-2d833a1a2f42
X-User-Id: 018f95f2-0f08-7f85-9b31-2d833a1a2f43
Content-Type: application/json
```

Payload:

```json
{
  "product_id": "018f95f2-0f08-7f85-9b31-2d833a1a2f41",
  "type": "purchase",
  "quantity": 5,
  "reason": "Compra de reposicao",
  "note": "Nota 123",
  "unit_cost_in_cents": 2590
}
```

Tipos de entrada aceitos:

```text
purchase
manual_adjustment
return
```

Criar produto:

```http
POST /api/v1/products
X-Tenant-Id: 018f95f2-0f08-7f85-9b31-2d833a1a2f41
Content-Type: application/json
```

Payload:

```json
{
  "name": "Filtro de oleo",
  "sku": "FO-001",
  "barcode": "7891234567890",
  "category": "Filtros",
  "brand": "Mann",
  "supplier": "Auto Pecas Central",
  "minimum_stock": 3,
  "cost_in_cents": 2590,
  "currency": "BRL"
}
```

Editar produto:

```http
PATCH /api/v1/products/018f95f2-0f08-7f85-9b31-2d833a1a2f41
X-Tenant-Id: 018f95f2-0f08-7f85-9b31-2d833a1a2f42
Content-Type: application/json
```

Payload:

```json
{
  "name": "Filtro de oleo atualizado",
  "sku": "FO-002",
  "barcode": "7891234567899",
  "category": "Filtros",
  "brand": "Mahle",
  "supplier": "Auto Pecas Central",
  "minimum_stock": 5,
  "cost_in_cents": 3190,
  "currency": "BRL"
}
```

## Estado Atual

O setup inicial do Laravel esta criado e validado com Docker.

A Fase 0 da fundacao tecnica do backend tambem esta implementada com:

- Estrutura inicial em `app/Modules`.
- Contratos compartilhados para `InputDto`, `OutputDto`, `UseCase` e `JsonPresenter`.
- Modulo `Tenant` inicial.
- `TenantContext` resolvido por middleware.
- Middleware `tenant` usando o header `X-Tenant-Id`.
- Migration da tabela `tenants`.
- Rotas API em `routes/api.php`.
- UC04 - Cadastrar produto/peca.
- UC05 - Editar produto/peca.
- UC06 - Consultar estoque, versao inicial baseada no catalogo.
- UC07 - Registrar entrada de estoque.
- UC08 - Registrar saida de estoque.
- UC09 - Registrar ajuste manual.
- UC10 - Gerar alerta de estoque minimo.
- UC11 - Gerar alerta de estoque zerado.
- UC12 - Visualizar dashboard.
- UC17 - Consultar historico de movimentacoes.
- Migration da tabela `products`.
- Migrations das tabelas `inventory_items` e `stock_movements`.
- Modulo `Catalog` inicial.
- Modulo `Inventory` inicial.

Validacoes ja realizadas:

```bash
docker compose exec backend php artisan --version
docker compose exec backend php artisan migrate:status
docker compose exec backend php artisan route:list --path=api
docker compose exec backend php artisan test
```

Resultado esperado:

```text
Laravel Framework 13.12.0
```

As migrations iniciais devem aparecer como `Ran`.

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
  frontend/
  docker-compose.yml
```

## Subir O Ambiente

Execute os comandos a partir da raiz do projeto:

```bash
docker compose up -d
```

A aplicacao ficara disponivel em:

```text
Backend API: http://localhost:8080
Front-end:   http://localhost:3000
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

Rodar seeders de desenvolvimento:

```bash
docker compose exec backend php artisan db:seed
```

Recriar banco local com migrations e seeders:

```bash
docker compose exec backend php artisan migrate:fresh --seed
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

## Dados De Desenvolvimento

O seeder padrao cria uma oficina demo com produtos, estoque, veiculos, usuarios por perfil e ordens de servico.

Tenant demo:

```text
018f95f2-0f08-7f85-9b31-2d833a1a2000
```

Usuarios demo:

| Perfil | E-mail | Senha |
| --- | --- | --- |
| owner | owner@autoestoque.test | password |
| manager | manager@autoestoque.test | password |
| admin | admin@autoestoque.test | password |
| mechanic | mechanic@autoestoque.test | password |

O dataset inclui:

- 5 produtos.
- 5 saldos de estoque.
- 4 movimentacoes de estoque.
- 2 veiculos.
- 1 OS aberta.
- 1 OS finalizada com baixa de estoque vinculada formalmente.

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

## Documentacao OpenAPI

A especificacao OpenAPI da API v1 esta em:

```text
backend/docs/openapi.yaml
```

Ela documenta:

- endpoints disponiveis;
- payloads de entrada;
- respostas principais;
- autenticacao `Authorization: Bearer`;
- matriz de permissoes por perfil.

Validar sintaxe YAML localmente:

```bash
ruby -e "require 'yaml'; YAML.load_file('backend/docs/openapi.yaml'); puts 'ok'"
```

Visualizar com Swagger UI:

```bash
docker run --rm -p 8081:8080 -e SWAGGER_JSON=/openapi.yaml -v "$PWD/backend/docs/openapi.yaml:/openapi.yaml" swaggerapi/swagger-ui
```

Depois acesse:

```text
http://localhost:8081
```

## API Inicial

Rotas disponiveis na fundacao tecnica:

```text
GET /api/v1/health
POST /api/v1/auth/login
POST /api/v1/auth/logout
POST /api/v1/auth/forgot-password
POST /api/v1/auth/reset-password
GET /api/v1/context/tenant
GET /api/v1/users
POST /api/v1/users
PATCH /api/v1/users/{user}
PATCH /api/v1/users/{user}/deactivate
GET /api/v1/dashboard
GET /api/v1/dashboard/most-consumed-products
GET /api/v1/stock
GET /api/v1/inventory/alerts/minimum-stock
GET /api/v1/inventory/alerts/zero-stock
GET /api/v1/inventory/movements
POST /api/v1/inventory/adjustments
POST /api/v1/inventory/entries
POST /api/v1/inventory/outputs
POST /api/v1/products
PATCH /api/v1/products/{product}
GET /api/v1/service-orders
POST /api/v1/service-orders
GET /api/v1/service-orders/{serviceOrder}
PATCH /api/v1/service-orders/{serviceOrder}/finish
POST /api/v1/service-orders/{serviceOrder}/parts
GET /api/v1/vehicles
POST /api/v1/vehicles
```

### Autenticar Usuario

```http
POST /api/v1/auth/login
Content-Type: application/json
```

Payload:

```json
{
  "email": "admin@oficina.com",
  "password": "secret",
  "token_name": "mobile"
}
```

Resposta:

```json
{
  "data": {
    "access_token": "token-opaco",
    "token_type": "Bearer",
    "user": {
      "id": "1",
      "name": "Admin Oficina",
      "email": "admin@oficina.com",
      "role": "admin"
    },
    "tenant": {
      "id": "uuid-do-tenant"
    }
  }
}
```

Nesta versao, o token e persistido em `user_access_tokens` usando hash SHA-256. As rotas protegidas usam `Authorization: Bearer {access_token}`; o tenant e o usuario atual sao resolvidos a partir do token autenticado.

### Permissoes Por Perfil

Roles aceitos:

- `owner`
- `manager`
- `admin`
- `mechanic`

Matriz atual:

- `owner`, `manager` e `admin`: podem acessar gestao de usuarios, dashboard, alertas, historico de movimentacoes, cadastro de produtos e movimentacoes manuais de estoque.
- `mechanic`: pode consultar estoque e operar rotinas da oficina, incluindo veiculos, ordens de servico, adicao de pecas e finalizacao de OS.

Quando o perfil autenticado nao tem permissao para a rota, a API retorna `403 Forbidden`.

### Encerrar Sessao

```http
POST /api/v1/auth/logout
Authorization: Bearer {access_token}
```

Revoga o token atual. Depois do logout, o mesmo token nao pode mais acessar rotas protegidas.

### Recuperar Senha

Solicitar recuperacao:

```http
POST /api/v1/auth/forgot-password
Content-Type: application/json
```

```json
{
  "email": "admin@oficina.com"
}
```

A resposta e generica e nao revela se o e-mail existe.

Redefinir senha:

```http
POST /api/v1/auth/reset-password
Content-Type: application/json
```

```json
{
  "email": "admin@oficina.com",
  "token": "token-recebido-por-email",
  "password": "new-secret",
  "password_confirmation": "new-secret"
}
```

### Gerenciar Usuarios Da Oficina

Listar usuarios:

```http
GET /api/v1/users
Authorization: Bearer {access_token}
```

Criar usuario:

```http
POST /api/v1/users
Authorization: Bearer {access_token}
Content-Type: application/json
```

```json
{
  "name": "Mecanico Oficina",
  "email": "mecanico@oficina.com",
  "password": "secret123",
  "role": "mechanic",
  "status": "active"
}
```

Editar usuario:

```http
PATCH /api/v1/users/{user}
Authorization: Bearer {access_token}
```

Inativar usuario:

```http
PATCH /api/v1/users/{user}/deactivate
Authorization: Bearer {access_token}
```

Roles aceitos: `owner`, `manager`, `admin` e `mechanic`.

Nesta versao, o limite do plano Starter e aplicado como ate 3 usuarios ativos por tenant.

### Visualizar Dashboard

```http
GET /api/v1/dashboard
Authorization: Bearer {access_token}
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

### Consultar Produtos Mais Consumidos

```http
GET /api/v1/dashboard/most-consumed-products
Authorization: Bearer {access_token}
```

Filtros aceitos:

- `period_from`: inicio do periodo. Quando omitido, usa o inicio do mes atual.
- `period_to`: fim do periodo. Quando omitido, usa a data atual.
- `limit`: entre `1` e `100`.

O ranking considera apenas movimentacoes com `direction` igual a `output`.

### Gerar Alertas De Estoque Minimo

```http
GET /api/v1/inventory/alerts/minimum-stock
Authorization: Bearer {access_token}
```

Filtros aceitos:

- `limit`: entre `1` e `100`

Nesta versao, os alertas sao calculados sob demanda a partir de `products.minimum_stock` e `inventory_items.current_stock`. Produtos com saldo `0` ficam reservados para o alerta de estoque zerado.

### Gerar Alertas De Estoque Zerado

```http
GET /api/v1/inventory/alerts/zero-stock
Authorization: Bearer {access_token}
```

Filtros aceitos:

- `limit`: entre `1` e `100`

Nesta versao, os alertas sao calculados sob demanda. Produtos sem movimentacao tambem aparecem como estoque zerado, pois o saldo considerado e `0`.

### Consultar Historico De Movimentacoes

```http
GET /api/v1/inventory/movements
Authorization: Bearer {access_token}
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
Authorization: Bearer {access_token}
```

Quando uma movimentacao tiver sido gerada pela finalizacao de uma ordem de servico, a resposta inclui `service_order` com `id` da ordem e `item_id` da peca da OS que originou a baixa. Movimentacoes manuais ou sem vinculo retornam `service_order` como `null`.

### Registrar Ajuste Manual De Estoque

```http
POST /api/v1/inventory/adjustments
Authorization: Bearer {access_token}
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
Authorization: Bearer {access_token}
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
Authorization: Bearer {access_token}
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

A rota `/api/v1/context/tenant` retorna o tenant resolvido a partir do usuario autenticado:

```http
Authorization: Bearer {access_token}
```

Todas as rotas protegidas resolvem tenant e usuario atual a partir do token Bearer.

Consultar estoque:

```http
GET /api/v1/stock?search=filtro
Authorization: Bearer {access_token}
```

A consulta retorna produtos cadastrados no tenant atual. Produtos sem movimentacao retornam `current_stock` igual a `0` e `stock_status` igual a `zero`. Produtos com entradas registradas retornam `current_stock` real a partir do modulo `Inventory`.

Registrar entrada de estoque:

```http
POST /api/v1/inventory/entries
Authorization: Bearer {access_token}
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
Authorization: Bearer {access_token}
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
Authorization: Bearer {access_token}
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

### Cadastrar Veiculo

```http
POST /api/v1/vehicles
Authorization: Bearer {access_token}
Content-Type: application/json
```

Payload:

```json
{
  "plate": "ABC-1D23",
  "brand": "Chevrolet",
  "model": "Onix",
  "year": 2020,
  "owner_name": "Joao Silva",
  "owner_phone": "11999990000"
}
```

A placa e normalizada para letras maiusculas e sem separadores. Placas duplicadas dentro do mesmo tenant retornam `409 Conflict`; a mesma placa pode existir em tenants diferentes.

### Listar Veiculos

```http
GET /api/v1/vehicles?search=onix&limit=50
Authorization: Bearer {access_token}
```

Filtros aceitos:

- `search`: busca por placa, marca, modelo, proprietario ou telefone.
- `limit`: entre `1` e `100`.

### Criar Ordem De Servico

```http
POST /api/v1/service-orders
Authorization: Bearer {access_token}
Content-Type: application/json
```

Payload:

```json
{
  "vehicle_id": "018f95f2-0f08-7f85-9b31-2d833a1a2f43",
  "customer_name": "Joao Silva",
  "services_description": "Troca de oleo e filtros",
  "observations": "Cliente aguardando"
}
```

A ordem e criada com status inicial `open`. O veiculo deve pertencer ao tenant atual; caso contrario, a API retorna `404 Not Found`.

### Listar Ordens De Servico

```http
GET /api/v1/service-orders?status=open&search=ABC1D23&limit=50
Authorization: Bearer {access_token}
```

Filtros aceitos:

- `status`: `open` ou `finished`.
- `search`: busca por cliente, servico, placa ou proprietario.
- `opened_from`: data inicial de abertura.
- `opened_to`: data final de abertura.
- `limit`: entre `1` e `100`.

### Detalhar Ordem De Servico

```http
GET /api/v1/service-orders/018f95f2-0f08-7f85-9b31-2d833a1a2f43
Authorization: Bearer {access_token}
```

A resposta inclui dados da ordem, veiculo vinculado e pecas adicionadas com nome, SKU e quantidade. Quando a OS ja foi finalizada e gerou baixas de estoque, cada peca tambem retorna `movements` com as movimentacoes vinculadas formalmente pela tabela `service_order_stock_movements`.

### Adicionar Peca A Ordem De Servico

```http
POST /api/v1/service-orders/018f95f2-0f08-7f85-9b31-2d833a1a2f43/parts
Authorization: Bearer {access_token}
Content-Type: application/json
```

Payload:

```json
{
  "product_id": "018f95f2-0f08-7f85-9b31-2d833a1a2f45",
  "quantity": 2
}
```

A peca fica vinculada a ordem de servico, mas o estoque ainda nao e baixado. Nesta versao, o saldo disponivel e validado antes da vinculacao para evitar reservar quantidade maior que o estoque atual.

### Finalizar Ordem De Servico

```http
PATCH /api/v1/service-orders/018f95f2-0f08-7f85-9b31-2d833a1a2f43/finish
Authorization: Bearer {access_token}
```

A finalizacao registra uma saida de estoque para cada peca vinculada a ordem usando `type` igual a `service_consumption`, atualiza o saldo dos itens e marca a ordem como `finished`. Cada movimentacao gerada tambem fica vinculada formalmente ao item da ordem na tabela `service_order_stock_movements`, permitindo rastrear qual peca da OS originou cada baixa de estoque. O processo e executado em transacao para evitar baixa parcial.

## Estado Atual

O setup inicial do Laravel esta criado e validado com Docker.

A Fase 0 da fundacao tecnica do backend tambem esta implementada com:

- Estrutura inicial em `app/Modules`.
- Contratos compartilhados para `InputDto`, `OutputDto`, `UseCase` e `JsonPresenter`.
- Modulo `Tenant` inicial.
- `TenantContext` resolvido pelo token autenticado.
- Middleware `auth.api` usando `Authorization: Bearer`.
- Middleware `role` para permissoes por perfil.
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
- UC18 - Consultar produtos mais consumidos.
- UC01 - Autenticar usuario, versao inicial com token de API.
- UC02 - Recuperar senha.
- UC03 - Gerenciar usuarios da oficina.
- UC13 - Cadastrar veiculo.
- UC14 - Criar ordem de servico.
- UC15 - Adicionar peca a ordem de servico.
- UC16 - Finalizar ordem de servico com baixa automatica.
- Migration de campos de identidade em `users`.
- Migration da tabela `user_access_tokens`.
- Migration da tabela `products`.
- Migration da tabela `vehicles`.
- Migration da tabela `service_orders`.
- Migration da tabela `service_order_items`.
- Migration da tabela `service_order_stock_movements`.
- Migrations das tabelas `inventory_items` e `stock_movements`.
- Modulo `Catalog` inicial.
- Modulo `Inventory` inicial.
- Modulo `Workshop` inicial.

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

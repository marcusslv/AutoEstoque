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
```

A rota `/api/v1/context/tenant` valida o tenant temporario usando o header:

```http
X-Tenant-Id: 018f95f2-0f08-7f85-9b31-2d833a1a2f41
```

Esse header sera usado enquanto o fluxo completo de autenticacao e multiempresa ainda nao estiver implementado.

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

# Setup Do Backend Com Docker

Este documento explica como subir o ambiente local do backend do AutoEstoque usando Docker.

## 1. Objetivo

O setup foi preparado para rodar a API Laravel com:

- Laravel 13.
- PHP 8.4 FPM.
- Nginx.
- PostgreSQL.
- Redis.
- Laravel Queue.
- Laravel Scheduler.

Como o projeto Laravel ainda nao foi criado no repositorio, o Docker esta configurado para usar a pasta `backend/` como raiz da aplicacao.

## 2. Estrutura Criada

```text
docker-compose.yml
docker/
  nginx/
    default.conf
  php/
    Dockerfile
    php.ini
docs/
  arquitetura/
    setup-backend-docker.md
```

Quando o Laravel for criado, a estrutura esperada sera:

```text
backend/
  app/
  bootstrap/
  config/
  database/
  public/
  routes/
  artisan
  composer.json
```

## 3. Servicos Do Docker Compose

| Servico | Funcao | Porta local |
| --- | --- | --- |
| `backend` | PHP 8.4 FPM com Composer e extensoes do Laravel 13 | Interna |
| `nginx` | Servidor HTTP para acessar a API | `8080` |
| `postgres` | Banco de dados PostgreSQL | `5432` |
| `redis` | Cache, sessoes e filas | `6379` |
| `queue` | Worker do Laravel Queue | Perfil `workers` |
| `scheduler` | Laravel Scheduler | Perfil `workers` |

## 4. Variaveis Padrao

O ambiente Docker usa os seguintes dados para banco e Redis:

```env
DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=autoestoque
DB_USERNAME=autoestoque
DB_PASSWORD=secret

REDIS_HOST=redis
REDIS_PORT=6379

CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
```

Esses valores tambem devem ser configurados no arquivo `backend/.env` depois que o Laravel for criado.

## 5. Criar O Projeto Laravel

O backend foi criado com Laravel 13, usando o template atual do `laravel/laravel`.

Primeiro, construa a imagem PHP:

```bash
docker compose build backend
```

Para criar o Laravel dentro da pasta `backend/` em uma instalacao nova:

```bash
docker compose run --rm backend composer create-project laravel/laravel .
```

Se a pasta `backend/` ainda nao existir, o Docker Compose cria automaticamente ao montar o volume.

## 6. Configurar O `.env` Do Laravel

Depois de criar o Laravel, ajuste o arquivo `backend/.env`.

Exemplo dos principais campos:

```env
APP_NAME=AutoEstoque
APP_ENV=local
APP_DEBUG=true
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

Gere a chave da aplicacao:

```bash
docker compose run --rm backend php artisan key:generate
```

## 7. Subir O Ambiente

Suba os servicos principais:

```bash
docker compose up -d
```

A API ficara disponivel em:

```text
http://localhost:8080
```

## 8. Rodar Migrations

Depois que o Laravel estiver configurado:

```bash
docker compose exec backend php artisan migrate
```

## 9. Rodar Comandos Do Laravel

Composer:

```bash
docker compose exec backend composer install
```

Artisan:

```bash
docker compose exec backend php artisan about
```

Testes:

```bash
docker compose exec backend php artisan test
```

Tinker:

```bash
docker compose exec backend php artisan tinker
```

## 10. Rodar Queue E Scheduler

Queue e Scheduler estao configurados com profile `workers`.

Para subir tudo com workers:

```bash
docker compose --profile workers up -d
```

Para subir somente os servicos principais, sem workers:

```bash
docker compose up -d
```

## 11. Acessar Banco E Redis

PostgreSQL via container:

```bash
docker compose exec postgres psql -U autoestoque -d autoestoque
```

Redis CLI:

```bash
docker compose exec redis redis-cli
```

## 12. Parar O Ambiente

Parar containers:

```bash
docker compose down
```

Parar containers e remover volumes de banco/redis:

```bash
docker compose down -v
```

Use `down -v` apenas quando quiser apagar os dados locais.

## 13. Observacoes Importantes

- A aplicacao Laravel deve ficar em `backend/`.
- A versao atual usada no backend e Laravel 13.
- O Nginx aponta para `backend/public`.
- O container `backend` usa PHP 8.4, alinhado com a decisao tecnica do projeto.
- O PostgreSQL e o Redis ficam em volumes Docker persistentes.
- O `queue` e o `scheduler` dependem do arquivo `artisan`; por isso ficam em profile separado.
- Em ambiente local, o usuario do container PHP roda como `www-data`.

## 14. Proximo Passo Recomendado

Depois de validar o setup Docker, o proximo passo e criar o projeto Laravel e iniciar a estrutura modular:

```text
backend/app/Modules/
  Catalog/
  Inventory/
  Identity/
  Workshop/
```

O primeiro caso de uso recomendado para implementacao e:

```text
UC04 - Cadastrar Produto/Peca
```

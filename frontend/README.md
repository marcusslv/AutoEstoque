# AutoEstoque Front-end

Painel web do AutoEstoque usando Nuxt, Vue, TypeScript, Tailwind CSS, Shadcn Vue e Pinia.

## Requisitos

- Node 20 ou superior.
- pnpm 10 ou superior.

Para rodar via Docker Compose, nao e necessario instalar Node ou pnpm diretamente na maquina.

## Comandos

Instalar dependencias:

```bash
pnpm install
```

Rodar em desenvolvimento:

```bash
pnpm dev
```

O script `dev` define `TMPDIR=/tmp` para manter o socket interno do Vite em um caminho curto no macOS.

Rodar com Docker Compose a partir da raiz do projeto:

```bash
docker compose up -d frontend
```

O front-end ficara disponivel em:

```text
http://localhost:3000
```

Variavel principal:

```env
NUXT_PUBLIC_API_BASE_URL=http://localhost:8080/api/v1
```

## Testes

Rodar testes unitarios:

```bash
pnpm test
```

Rodar testes E2E com Playwright:

```bash
pnpm test:e2e
```

Rodar a validacao principal do front-end:

```bash
pnpm exec vue-tsc --noEmit
pnpm test
pnpm test:e2e
pnpm build
```

## Arquitetura

A estrutura segue Atomic Design para componentes visuais e modulos por dominio para regras do AutoEstoque.

Documentacao:

```text
../docs/arquitetura/arquitetura-frontend-nuxt.md
../docs/arquitetura/sequencia-implementacao-frontend-nuxt.md
```

# AutoEstoque Front-end

Painel web do AutoEstoque usando Nuxt, Vue, TypeScript, Tailwind CSS, Shadcn Vue e Pinia.

## Requisitos

- Node 20 ou superior.
- pnpm 10 ou superior.

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

Variavel principal:

```env
NUXT_PUBLIC_API_BASE_URL=http://localhost:8080/api/v1
```

## Arquitetura

A estrutura segue Atomic Design para componentes visuais e modulos por dominio para regras do AutoEstoque.

Documentacao:

```text
../docs/arquitetura/arquitetura-frontend-nuxt.md
../docs/arquitetura/sequencia-implementacao-frontend-nuxt.md
```

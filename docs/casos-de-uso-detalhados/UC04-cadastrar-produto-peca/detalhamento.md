# UC04 - Cadastrar Produto/Peca

## Objetivo

Permitir que a oficina registre uma peca no estoque.

## Ator Principal

Responsavel Administrativo.

## Atores Secundarios

- Sistema.

## Prioridade

Alta.

## Fase

MVP.

## Pre-condicoes

- Usuario autenticado.
- Usuario pertence a uma oficina.
- Usuario possui permissao para gerenciar produtos.

## Pos-condicoes

- Produto cadastrado.
- Produto disponivel para consulta e movimentacao.

## Fluxo Principal

1. Usuario acessa o modulo de produtos.
2. Usuario clica em novo produto.
3. Usuario informa nome, SKU, codigo de barras, categoria, marca, fornecedor, estoque minimo e custo.
4. Sistema valida os campos obrigatorios.
5. Sistema verifica se SKU ou codigo de barras ja existem na mesma oficina.
6. Sistema salva o produto.
7. Produto fica disponivel para consulta e movimentacoes.

## Fluxos Alternativos

### FA01 - SKU Ja Cadastrado

1. Usuario informa SKU ja usado na mesma oficina.
2. Sistema bloqueia o cadastro.
3. Sistema informa o conflito.

### FA02 - Codigo De Barras Vazio

1. Usuario nao informa codigo de barras.
2. Sistema permite salvar, caso o campo nao seja obrigatorio.

### FA03 - Estoque Minimo Vazio

1. Usuario nao informa estoque minimo.
2. Sistema usa zero como padrao ou solicita preenchimento, conforme regra definida.

## Regras De Negocio

- SKU nao deve se repetir dentro da mesma oficina.
- Codigo de barras pode ser usado para consulta rapida.
- Produto deve pertencer a apenas uma oficina.
- Custo deve ser maior ou igual a zero.

## Resultado Esperado

Produto cadastrado no tenant correto e disponivel para movimentacao.


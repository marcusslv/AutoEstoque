# UC06 - Consultar Estoque

## Objetivo

Permitir a consulta rapida de produtos, saldos e situacao de estoque.

## Ator Principal

Administrativo ou Mecanico.

## Atores Secundarios

- Sistema.

## Prioridade

Alta.

## Fase

MVP.

## Pre-condicoes

- Usuario autenticado.
- Existencia de produtos cadastrados.

## Pos-condicoes

- Usuario visualiza a disponibilidade das pecas.

## Fluxo Principal

1. Usuario acessa o modulo de estoque ou produtos.
2. Sistema exibe lista de pecas cadastradas.
3. Usuario pesquisa por nome, SKU, categoria, marca ou codigo de barras.
4. Sistema filtra os resultados.
5. Usuario visualiza estoque atual, estoque minimo, custo e status.

## Fluxos Alternativos

### FA01 - Nenhum Produto Encontrado

1. Usuario pesquisa por um termo sem resultado.
2. Sistema exibe estado vazio.

### FA02 - Produto Com Estoque Zerado

1. Sistema identifica produto com saldo zero.
2. Sistema exibe status de estoque zerado.

### FA03 - Produto Abaixo Do Minimo

1. Sistema identifica produto abaixo do minimo.
2. Sistema destaca necessidade de reposicao.

## Regras De Negocio

- Consulta deve retornar apenas produtos da oficina do usuario.
- Produtos abaixo do minimo devem ser destacados.
- Produtos zerados devem ter status especifico.

## Resultado Esperado

Usuario identifica rapidamente a disponibilidade das pecas.


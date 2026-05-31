# UC18 - Consultar Produtos Mais Consumidos

## Objetivo

Identificar quais pecas tem maior consumo na oficina.

## Ator Principal

Proprietario/Gerente.

## Atores Secundarios

- Sistema.

## Prioridade

Media.

## Fase

MVP/Fase 3.

## Pre-condicoes

- Usuario autenticado.
- Existencia de movimentacoes de saida.

## Pos-condicoes

- Ranking de produtos consumidos exibido.

## Fluxo Principal

1. Usuario acessa dashboard ou relatorio de consumo.
2. Sistema calcula o consumo por produto em um periodo.
3. Sistema ordena os produtos mais consumidos.
4. Usuario visualiza ranking de consumo.

## Fluxos Alternativos

### FA01 - Sem Movimentacoes No Periodo

1. Sistema nao encontra saidas no periodo.
2. Sistema exibe estado vazio.

### FA02 - Periodo Nao Informado

1. Usuario nao informa periodo.
2. Sistema utiliza periodo padrao.

## Regras De Negocio

- Ranking deve considerar apenas movimentacoes de saida.
- Consumo deve ser calculado por oficina.
- Periodo padrao pode ser o mes atual.

## Resultado Esperado

Gerente identifica pecas de maior giro e melhora o planejamento de compras.


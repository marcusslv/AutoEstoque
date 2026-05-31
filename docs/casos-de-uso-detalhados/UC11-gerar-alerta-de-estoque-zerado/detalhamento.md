# UC11 - Gerar Alerta De Estoque Zerado

## Objetivo

Avisar quando uma peca nao possui saldo disponivel.

## Ator Principal

Sistema.

## Atores Secundarios

- Proprietario/Gerente.
- Responsavel Administrativo.

## Prioridade

Alta.

## Fase

MVP.

## Pre-condicoes

- Produto cadastrado.
- Estoque atual igual a zero.

## Pos-condicoes

- Alerta de estoque zerado criado ou atualizado.

## Fluxo Principal

1. Sistema identifica alteracao no saldo.
2. Sistema verifica que o estoque atual e zero.
3. Sistema cria ou atualiza alerta de estoque zerado.
4. Sistema exibe o alerta no dashboard.
5. Sistema disponibiliza o alerta para notificacao mobile.

## Fluxos Alternativos

### FA01 - Produto Recebe Entrada

1. Produto recebe nova entrada de estoque.
2. Sistema verifica que o saldo ficou maior que zero.
3. Sistema remove ou marca o alerta como resolvido.

### FA02 - Alerta Ja Existente

1. Sistema identifica alerta aberto de estoque zerado.
2. Sistema atualiza o alerta existente.

## Regras De Negocio

- Estoque zerado deve ter prioridade visual maior que estoque minimo.
- Alerta nao deve ser duplicado para o mesmo produto.
- Alerta deve ser resolvido quando o saldo for regularizado.

## Resultado Esperado

Oficina identifica pecas indisponiveis rapidamente.


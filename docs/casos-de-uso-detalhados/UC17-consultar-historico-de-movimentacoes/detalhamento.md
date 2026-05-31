# UC17 - Consultar Historico De Movimentacoes

## Objetivo

Permitir auditoria e rastreabilidade das entradas, saidas e ajustes de estoque.

## Ator Principal

Gerente ou Administrativo.

## Atores Secundarios

- Sistema.

## Prioridade

Alta.

## Fase

MVP.

## Pre-condicoes

- Usuario autenticado.
- Usuario com permissao para consultar movimentacoes.

## Pos-condicoes

- Historico exibido conforme filtros informados.

## Fluxo Principal

1. Usuario acessa o historico de movimentacoes.
2. Sistema exibe movimentacoes da oficina.
3. Usuario filtra por periodo, produto, tipo, motivo ou usuario.
4. Sistema atualiza a lista conforme os filtros.
5. Usuario visualiza data, produto, quantidade, tipo, motivo e responsavel.

## Fluxos Alternativos

### FA01 - Nenhuma Movimentacao Encontrada

1. Usuario aplica filtros sem resultados.
2. Sistema exibe estado vazio.

### FA02 - Usuario Sem Permissao

1. Usuario tenta acessar historico sem permissao.
2. Sistema bloqueia o acesso.

## Regras De Negocio

- Historico deve ser imutavel para fins de auditoria.
- Usuario deve ver apenas movimentacoes da sua oficina.
- Toda movimentacao deve possuir usuario, data, produto, tipo, quantidade e motivo.

## Resultado Esperado

Usuario consegue rastrear alteracoes de estoque.


# UC15 - Adicionar Pecas A Ordem De Servico

## Objetivo

Vincular pecas utilizadas a uma ordem de servico.

## Ator Principal

Mecanico.

## Atores Secundarios

- Sistema.

## Prioridade

Media.

## Fase

Fase 2.

## Pre-condicoes

- Ordem de servico aberta.
- Produto cadastrado.
- Usuario autenticado.

## Pos-condicoes

- Peca vinculada a ordem de servico.
- Estoque ainda nao baixado, salvo se a regra do produto definir baixa imediata.

## Fluxo Principal

1. Usuario acessa uma ordem de servico aberta.
2. Usuario seleciona a opcao de adicionar peca.
3. Usuario pesquisa e seleciona o produto.
4. Usuario informa a quantidade utilizada.
5. Sistema valida disponibilidade.
6. Sistema adiciona a peca a ordem de servico.

## Fluxos Alternativos

### FA01 - Estoque Insuficiente

1. Usuario informa quantidade maior que o saldo.
2. Sistema alerta o usuario.
3. Usuario ajusta a quantidade ou solicita ajuste autorizado.

### FA02 - Produto Nao Encontrado

1. Usuario pesquisa produto inexistente.
2. Sistema permite nova busca.

## Regras De Negocio

- Apenas ordens abertas podem receber pecas.
- Quantidade deve ser maior que zero.
- Sistema deve validar disponibilidade antes da finalizacao.

## Resultado Esperado

Pecas ficam vinculadas a ordem de servico para baixa posterior.


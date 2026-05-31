# UC07 - Registrar Entrada De Estoque

## Objetivo

Aumentar a quantidade disponivel de uma peca.

## Ator Principal

Responsavel Administrativo.

## Atores Secundarios

- Sistema.

## Prioridade

Alta.

## Fase

MVP.

## Pre-condicoes

- Produto cadastrado.
- Usuario autenticado.
- Usuario com permissao para movimentar estoque.

## Pos-condicoes

- Estoque do produto aumentado.
- Movimentacao registrada.
- Alertas atualizados.

## Fluxo Principal

1. Usuario acessa o produto ou o modulo de movimentacoes.
2. Usuario seleciona o tipo de entrada: compra, ajuste manual ou devolucao.
3. Usuario seleciona o produto.
4. Usuario informa quantidade, custo, motivo e observacao, quando aplicavel.
5. Sistema valida se a quantidade e maior que zero.
6. Sistema registra a movimentacao.
7. Sistema atualiza o estoque atual do produto.
8. Sistema remove ou atualiza alertas de estoque, caso o saldo tenha sido regularizado.

## Fluxos Alternativos

### FA01 - Quantidade Invalida

1. Usuario informa quantidade menor ou igual a zero.
2. Sistema bloqueia o registro.
3. Sistema solicita uma quantidade valida.

### FA02 - Produto Inexistente

1. Usuario tenta registrar entrada para produto inexistente.
2. Sistema solicita a selecao de um produto valido.

### FA03 - Custo Nao Informado

1. Usuario nao informa custo na entrada.
2. Sistema utiliza o custo atual do cadastro, se a regra permitir.

## Regras De Negocio

- Entrada deve registrar usuario, data, produto, quantidade, tipo e motivo.
- Quantidade deve ser maior que zero.
- Entrada por compra pode atualizar custo medio em versao futura.

## Resultado Esperado

Estoque atualizado e movimentacao registrada com usuario, data e motivo.


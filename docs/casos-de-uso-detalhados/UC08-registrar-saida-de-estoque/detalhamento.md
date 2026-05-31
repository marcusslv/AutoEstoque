# UC08 - Registrar Saida De Estoque

## Objetivo

Reduzir o estoque de uma peca por consumo, perda, quebra ou ajuste.

## Ator Principal

Administrativo ou Mecanico.

## Atores Secundarios

- Sistema.

## Prioridade

Alta.

## Fase

MVP.

## Pre-condicoes

- Produto cadastrado.
- Usuario autenticado.
- Estoque disponivel, exceto quando a regra permitir estoque negativo.

## Pos-condicoes

- Estoque do produto reduzido.
- Movimentacao registrada.
- Alertas atualizados.

## Fluxo Principal

1. Usuario acessa o modulo de movimentacoes.
2. Usuario seleciona o tipo de saida: consumo em servico, perda, quebra ou ajuste manual.
3. Usuario seleciona o produto.
4. Usuario informa quantidade e motivo.
5. Sistema valida se ha saldo suficiente.
6. Sistema registra a movimentacao.
7. Sistema reduz o estoque atual.
8. Sistema verifica se o estoque ficou zerado ou abaixo do minimo.
9. Sistema gera alerta, se necessario.

## Fluxos Alternativos

### FA01 - Estoque Insuficiente

1. Usuario informa quantidade maior que o saldo disponivel.
2. Sistema bloqueia a saida ou solicita permissao especial.

### FA02 - Produto Sem Estoque

1. Usuario tenta baixar produto com saldo zero.
2. Sistema impede a baixa comum.
3. Sistema sugere ajuste autorizado, se aplicavel.

### FA03 - Motivo Nao Informado

1. Usuario nao informa motivo.
2. Sistema bloqueia o registro.

## Regras De Negocio

- Saida deve registrar usuario, data, produto, quantidade, tipo e motivo.
- Saida nao deve gerar saldo negativo, salvo regra autorizada.
- Saidas podem gerar alertas de estoque minimo ou zerado.

## Resultado Esperado

Estoque reduzido e historico de saida registrado.


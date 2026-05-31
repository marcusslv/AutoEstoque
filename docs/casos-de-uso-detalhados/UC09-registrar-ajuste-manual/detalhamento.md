# UC09 - Registrar Ajuste Manual

## Objetivo

Corrigir diferencas de estoque identificadas em inventarios ou conferencias.

## Ator Principal

Administrativo ou Gerente.

## Atores Secundarios

- Sistema.

## Prioridade

Alta.

## Fase

MVP.

## Pre-condicoes

- Usuario autenticado.
- Produto cadastrado.
- Usuario com permissao para ajuste manual.

## Pos-condicoes

- Saldo corrigido.
- Ajuste registrado com justificativa.

## Fluxo Principal

1. Usuario acessa o modulo de movimentacoes.
2. Usuario seleciona ajuste manual.
3. Usuario escolhe se o ajuste sera de entrada ou saida.
4. Usuario seleciona o produto.
5. Usuario informa quantidade e motivo.
6. Sistema registra a movimentacao.
7. Sistema atualiza o saldo do produto.

## Fluxos Alternativos

### FA01 - Usuario Sem Permissao

1. Usuario tenta registrar ajuste manual sem permissao.
2. Sistema bloqueia a operacao.

### FA02 - Motivo Vazio

1. Usuario nao informa justificativa.
2. Sistema exige o preenchimento do motivo.

### FA03 - Ajuste De Saida Com Saldo Insuficiente

1. Usuario tenta reduzir estoque alem do saldo disponivel.
2. Sistema bloqueia ou solicita autorizacao especial.

## Regras De Negocio

- Ajuste manual deve exigir justificativa.
- Ajuste manual deve ser rastreavel.
- Apenas perfis autorizados podem realizar ajuste.

## Resultado Esperado

Saldo corrigido com rastreabilidade da alteracao.


# UC05 - Editar Produto/Peca

## Objetivo

Permitir a atualizacao dos dados cadastrais de uma peca.

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
- Produto cadastrado.
- Usuario com permissao para editar produtos.

## Pos-condicoes

- Dados cadastrais do produto atualizados.
- Historico de movimentacoes preservado.

## Fluxo Principal

1. Usuario acessa a lista de produtos.
2. Usuario seleciona um produto.
3. Sistema exibe os dados atuais.
4. Usuario altera as informacoes desejadas.
5. Sistema valida os dados.
6. Sistema salva as alteracoes.

## Fluxos Alternativos

### FA01 - SKU Ja Existente

1. Usuario altera o SKU para um codigo ja usado.
2. Sistema bloqueia a alteracao.
3. Sistema informa o conflito.

### FA02 - Produto Com Movimentacoes

1. Usuario edita produto que possui historico.
2. Sistema permite alterar dados cadastrais.
3. Sistema preserva todo o historico de movimentacoes.

## Regras De Negocio

- Alteracoes cadastrais nao devem apagar historico.
- SKU deve continuar unico por oficina.
- Alterar custo cadastral nao deve recalcular movimentacoes antigas, salvo regra futura especifica.

## Resultado Esperado

Produto atualizado sem perda de rastreabilidade.


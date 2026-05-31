# UC10 - Gerar Alerta De Estoque Minimo

## Objetivo

Avisar quando uma peca precisa de reposicao.

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

- Produto possui estoque minimo configurado.
- Estoque atual esta menor ou igual ao estoque minimo.

## Pos-condicoes

- Alerta de estoque minimo criado ou atualizado.

## Fluxo Principal

1. Sistema identifica alteracao no saldo do produto.
2. Sistema compara estoque atual com estoque minimo.
3. Caso esteja abaixo ou igual ao minimo, sistema cria ou atualiza um alerta.
4. Alerta aparece no dashboard.
5. Alerta fica disponivel para notificacao no aplicativo.

## Fluxos Alternativos

### FA01 - Produto Sem Estoque Minimo

1. Produto nao possui estoque minimo configurado.
2. Sistema nao gera alerta de minimo.

### FA02 - Alerta Ja Existente

1. Sistema identifica alerta aberto para o produto.
2. Sistema atualiza a situacao em vez de criar duplicidade.

### FA03 - Estoque Regularizado

1. Produto recebe entrada de estoque.
2. Sistema identifica que o saldo ficou acima do minimo.
3. Sistema remove ou resolve o alerta.

## Regras De Negocio

- Alerta de minimo nao deve ser duplicado para o mesmo produto.
- Alerta deve pertencer ao tenant da oficina.
- Alerta deve refletir o saldo mais recente.

## Resultado Esperado

Oficina visualiza itens que precisam de reposicao.


# UC16 - Finalizar Ordem De Servico Com Baixa Automatica

## Objetivo

Dar baixa automatica nas pecas usadas durante o servico.

## Ator Principal

Mecanico/Sistema.

## Atores Secundarios

- Administrativo.
- Proprietario/Gerente.

## Prioridade

Alta.

## Fase

Fase 2.

## Pre-condicoes

- Ordem de servico aberta.
- Pecas adicionadas a ordem de servico.
- Pecas possuem estoque suficiente.

## Pos-condicoes

- Ordem de servico finalizada.
- Estoque atualizado.
- Movimentacoes de saida registradas.

## Fluxo Principal

1. Usuario revisa a ordem de servico.
2. Usuario confirma servicos e pecas utilizadas.
3. Usuario clica em finalizar.
4. Sistema valida estoque das pecas.
5. Sistema registra uma saida para cada peca usada.
6. Sistema atualiza o estoque.
7. Sistema marca a ordem de servico como finalizada.
8. Sistema gera alertas de reposicao, se necessario.

## Fluxos Alternativos

### FA01 - Peca Sem Estoque Suficiente

1. Sistema identifica saldo insuficiente.
2. Sistema bloqueia a finalizacao ou solicita ajuste autorizado.

### FA02 - Peca Removida Antes Da Finalizacao

1. Usuario remove uma peca da ordem.
2. Sistema nao baixa o item removido.

### FA03 - Falha Durante Baixa

1. Sistema falha ao registrar uma das movimentacoes.
2. Sistema mantem a ordem em aberto.
3. Sistema informa o erro.

## Regras De Negocio

- Finalizacao deve ser atomica: ou todas as baixas sao registradas, ou nenhuma.
- Cada peca utilizada deve gerar uma movimentacao de saida.
- Ordem finalizada nao deve gerar baixa duplicada.
- Baixas devem gerar alertas quando necessario.

## Resultado Esperado

Ordem de servico finalizada, estoque atualizado automaticamente e historico de consumo registrado.


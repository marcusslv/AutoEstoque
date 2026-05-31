# UC12 - Visualizar Dashboard

## Objetivo

Permitir que o gerente acompanhe os principais indicadores do estoque.

## Ator Principal

Proprietario/Gerente.

## Atores Secundarios

- Sistema.

## Prioridade

Alta.

## Fase

MVP.

## Pre-condicoes

- Usuario autenticado.
- Usuario com acesso ao dashboard.

## Pos-condicoes

- Indicadores exibidos para a oficina correta.

## Fluxo Principal

1. Usuario acessa o dashboard.
2. Sistema calcula os indicadores da oficina.
3. Sistema exibe quantidade total de produtos.
4. Sistema exibe produtos abaixo do minimo.
5. Sistema exibe valor total em estoque.
6. Sistema exibe produtos mais consumidos.
7. Sistema exibe movimentacoes do dia.

## Fluxos Alternativos

### FA01 - Sem Produtos Cadastrados

1. Sistema identifica que nao ha produtos cadastrados.
2. Sistema exibe indicadores zerados.

### FA02 - Sem Movimentacoes No Dia

1. Sistema identifica ausencia de movimentacoes no dia.
2. Sistema exibe lista vazia para movimentacoes recentes.

## Regras De Negocio

- Dashboard deve considerar apenas dados da oficina autenticada.
- Valor total em estoque deve considerar estoque atual multiplicado pelo custo.
- Movimentacoes do dia devem respeitar o fuso horario configurado.

## Resultado Esperado

Gerente acompanha a situacao do estoque em tempo real.


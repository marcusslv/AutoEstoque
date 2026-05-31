# UC13 - Cadastrar Veiculo

## Objetivo

Registrar os veiculos atendidos pela oficina.

## Ator Principal

Responsavel Administrativo.

## Atores Secundarios

- Sistema.

## Prioridade

Media.

## Fase

Fase 2.

## Pre-condicoes

- Usuario autenticado.
- Usuario com permissao para gerenciar veiculos.

## Pos-condicoes

- Veiculo cadastrado.
- Veiculo disponivel para ordens de servico.

## Fluxo Principal

1. Usuario acessa o modulo de veiculos.
2. Usuario clica em novo veiculo.
3. Usuario informa placa, marca, modelo, ano, proprietario e telefone.
4. Sistema valida os dados.
5. Sistema salva o veiculo vinculado a oficina.

## Fluxos Alternativos

### FA01 - Placa Ja Cadastrada

1. Usuario informa placa ja existente.
2. Sistema informa que o veiculo ja esta cadastrado.

### FA02 - Dados Incompletos

1. Usuario deixa campos obrigatorios em branco.
2. Sistema solicita o preenchimento dos campos.

## Regras De Negocio

- Placa deve ser unica por oficina.
- Veiculo deve estar vinculado ao tenant da oficina.
- Dados do proprietario devem estar disponiveis para contato.

## Resultado Esperado

Veiculo cadastrado e disponivel para ordens de servico.


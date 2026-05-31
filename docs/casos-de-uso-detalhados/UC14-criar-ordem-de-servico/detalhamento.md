# UC14 - Criar Ordem De Servico

## Objetivo

Registrar um servico executado em um veiculo.

## Ator Principal

Administrativo ou Mecanico.

## Atores Secundarios

- Sistema.

## Prioridade

Media.

## Fase

Fase 2.

## Pre-condicoes

- Veiculo cadastrado.
- Usuario autenticado.

## Pos-condicoes

- Ordem de servico criada em aberto.

## Fluxo Principal

1. Usuario acessa o modulo de ordens de servico.
2. Usuario seleciona ou cadastra um veiculo.
3. Usuario informa cliente, servicos realizados e observacoes.
4. Sistema salva a ordem de servico em aberto.
5. Sistema vincula a ordem de servico a oficina.

## Fluxos Alternativos

### FA01 - Veiculo Nao Cadastrado

1. Usuario nao encontra o veiculo.
2. Sistema permite cadastrar o veiculo antes de criar a ordem.

### FA02 - Dados Obrigatorios Ausentes

1. Usuario deixa dados obrigatorios em branco.
2. Sistema solicita correcao.

## Regras De Negocio

- Ordem de servico deve pertencer a uma oficina.
- Ordem deve iniciar com status em aberto.
- Ordem finalizada nao deve permitir alteracoes sem regra de reabertura.

## Resultado Esperado

Ordem de servico criada e disponivel para inclusao de pecas.


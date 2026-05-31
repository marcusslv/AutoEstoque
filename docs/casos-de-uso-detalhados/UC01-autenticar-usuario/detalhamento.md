# UC01 - Autenticar Usuario

## Objetivo

Permitir que um usuario autorizado acesse o sistema da oficina.

## Ator Principal

Usuario.

## Atores Secundarios

- Sistema.

## Prioridade

Alta.

## Fase

MVP.

## Pre-condicoes

- Usuario cadastrado.
- Usuario vinculado a uma oficina.
- Conta ativa.

## Pos-condicoes

- Usuario autenticado.
- Sessao criada.
- Usuario visualiza apenas dados da oficina vinculada.

## Fluxo Principal

1. Usuario acessa a tela de login.
2. Usuario informa e-mail e senha.
3. Sistema valida as credenciais.
4. Sistema verifica se a conta esta ativa.
5. Sistema identifica a oficina vinculada ao usuario.
6. Sistema cria a sessao de acesso.
7. Usuario e direcionado ao dashboard.

## Fluxos Alternativos

### FA01 - Credenciais Invalidas

1. Usuario informa e-mail ou senha incorretos.
2. Sistema rejeita o login.
3. Sistema informa que as credenciais sao invalidas.

### FA02 - Conta Inativa

1. Sistema identifica que a conta esta inativa.
2. Sistema bloqueia o acesso.
3. Sistema informa que o usuario deve entrar em contato com o administrador.

### FA03 - Usuario Com Mais De Uma Oficina

1. Sistema identifica mais de uma oficina vinculada ao usuario.
2. Sistema solicita a escolha da oficina.
3. Usuario seleciona a oficina desejada.
4. Sistema inicia a sessao no contexto selecionado.

## Regras De Negocio

- O usuario deve acessar apenas os dados do tenant selecionado.
- Nao deve existir sessao sem oficina vinculada.
- O sistema deve impedir acesso de contas inativas.

## Resultado Esperado

Usuario autenticado e com acesso restrito aos dados da sua oficina.


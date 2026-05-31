# UC02 - Recuperar Senha

## Objetivo

Permitir que um usuario recupere o acesso a conta quando esquecer a senha.

## Ator Principal

Usuario.

## Atores Secundarios

- Sistema.
- Servico de e-mail.

## Prioridade

Alta.

## Fase

MVP.

## Pre-condicoes

- Usuario possui e-mail cadastrado.
- Conta esta ativa.

## Pos-condicoes

- Usuario recebe instrucao para redefinir senha.
- Senha pode ser atualizada com seguranca.

## Fluxo Principal

1. Usuario acessa a opcao de recuperacao de senha.
2. Usuario informa o e-mail cadastrado.
3. Sistema valida se o e-mail existe e esta ativo.
4. Sistema gera token de recuperacao.
5. Sistema envia link de redefinicao por e-mail.
6. Usuario acessa o link.
7. Usuario informa nova senha.
8. Sistema valida a nova senha.
9. Sistema atualiza a senha.
10. Sistema invalida o token utilizado.

## Fluxos Alternativos

### FA01 - E-mail Nao Encontrado

1. Usuario informa um e-mail nao cadastrado.
2. Sistema exibe mensagem generica de seguranca.
3. Sistema nao revela se o e-mail existe ou nao.

### FA02 - Token Expirado

1. Usuario acessa link com token expirado.
2. Sistema bloqueia a redefinicao.
3. Sistema solicita uma nova recuperacao de senha.

### FA03 - Senha Invalida

1. Usuario informa senha fora do padrao minimo.
2. Sistema informa os criterios necessarios.
3. Usuario informa nova senha valida.

## Regras De Negocio

- Token de recuperacao deve ter validade limitada.
- Token deve ser inutilizado apos uso.
- Mensagens de recuperacao nao devem expor se um e-mail esta cadastrado.

## Resultado Esperado

Usuario recupera acesso a conta com uma nova senha.


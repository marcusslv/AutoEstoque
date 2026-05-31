# UC03 - Gerenciar Usuarios Da Oficina

## Objetivo

Permitir que o proprietario ou gerente cadastre e administre usuarios da oficina.

## Ator Principal

Proprietario/Gerente.

## Atores Secundarios

- Sistema.
- Usuario convidado.

## Prioridade

Alta.

## Fase

MVP.

## Pre-condicoes

- Usuario autenticado.
- Usuario com permissao administrativa.
- Oficina cadastrada.

## Pos-condicoes

- Usuario criado, atualizado, ativado ou inativado.
- Usuario permanece vinculado ao tenant correto.

## Fluxo Principal

1. Gerente acessa o modulo de usuarios.
2. Sistema exibe os usuarios cadastrados na oficina.
3. Gerente clica em novo usuario.
4. Gerente informa nome, e-mail, perfil e status.
5. Sistema valida os dados.
6. Sistema cria o usuario vinculado a oficina.
7. Sistema envia orientacao de acesso ou convite, conforme regra definida.

## Fluxos Alternativos

### FA01 - E-mail Ja Cadastrado

1. Gerente informa e-mail ja existente.
2. Sistema informa que o usuario ja esta cadastrado.
3. Gerente pode vincular o usuario existente, se a regra permitir.

### FA02 - Limite Do Plano Atingido

1. Gerente tenta criar usuario alem do limite contratado.
2. Sistema bloqueia o cadastro.
3. Sistema sugere upgrade de plano.

### FA03 - Inativar Usuario

1. Gerente seleciona um usuario ativo.
2. Gerente escolhe a opcao de inativar.
3. Sistema bloqueia novos acessos desse usuario.

## Regras De Negocio

- Plano Starter permite ate 3 usuarios.
- Plano Pro permite usuarios ilimitados.
- Um usuario inativo nao pode acessar o sistema.
- Usuarios devem operar apenas nos dados da oficina vinculada.

## Resultado Esperado

Usuarios da oficina gerenciados com seguranca e respeitando o plano contratado.


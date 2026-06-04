# UC19 - Gerenciar Configuracoes Da Oficina

## Objetivo

Permitir que o proprietario ou gerente configure dados e preferencias operacionais da oficina dentro do AutoEstoque.

## Ator Principal

Proprietario/Gerente.

## Atores Secundarios

- Sistema.
- Usuario administrativo.

## Prioridade

Media.

## Fase

MVP/Fase 2.

## Pre-condicoes

- Usuario autenticado.
- Usuario vinculado a uma oficina.
- Usuario com permissao para administrar configuracoes.
- Tenant da oficina existente.

## Pos-condicoes

- Configuracoes da oficina atualizadas.
- Alteracoes ficam vinculadas ao tenant correto.
- Historico de auditoria pode registrar usuario, data e campos alterados.

## Escopo Das Configuracoes

### Dados Da Oficina

- Nome fantasia.
- Razao social.
- Documento, como CNPJ ou CPF.
- Telefone.
- E-mail.
- Endereco.

### Configuracoes Operacionais

- Nome exibido no sistema.
- Moeda padrao.
- Fuso horario.
- Politica de estoque negativo.
- Parametro padrao para estoque minimo.
- Preferencia para baixa automatica ao finalizar ordem de servico.

### Configuracoes De Notificacao

- Receber alertas de estoque baixo.
- Receber alertas de estoque zerado.
- Canal de notificacao, quando disponivel.
- E-mail ou telefone de destino para notificacoes.

### Configuracoes De Plano

- Visualizar plano atual.
- Visualizar limite de usuarios.
- Visualizar recursos disponiveis.
- Solicitar upgrade de plano.

## Fluxo Principal

1. Usuario acessa o modulo de configuracoes.
2. Sistema carrega as configuracoes atuais da oficina.
3. Usuario altera os campos permitidos.
4. Sistema valida os dados informados.
5. Sistema salva as configuracoes no tenant da oficina.
6. Sistema registra a alteracao, quando auditoria estiver ativa.
7. Sistema informa que as configuracoes foram atualizadas.

## Fluxos Alternativos

### FA01 - Dados Invalidos

1. Usuario informa dados invalidos, como e-mail, telefone ou documento em formato incorreto.
2. Sistema bloqueia o salvamento.
3. Sistema informa os campos que precisam ser corrigidos.

### FA02 - Usuario Sem Permissao

1. Usuario sem permissao tenta acessar configuracoes.
2. Sistema bloqueia o acesso.
3. Sistema retorna erro de permissao.

### FA03 - Alteracao De Recurso Restrito Ao Plano

1. Usuario tenta habilitar recurso nao incluido no plano atual.
2. Sistema bloqueia a alteracao.
3. Sistema informa que o recurso depende de upgrade de plano.

### FA04 - Politica De Estoque Negativo Desabilitada

1. Usuario desabilita estoque negativo.
2. Sistema passa a bloquear saidas ou finalizacoes de OS que deixariam produto com saldo negativo.
3. Sistema mantem as movimentacoes anteriores sem recalculo.

## Regras De Negocio

- Configuracoes pertencem sempre a um unico tenant.
- Apenas perfis administrativos podem alterar configuracoes.
- Mecanicos podem, no maximo, consultar dados basicos da oficina se houver necessidade operacional.
- Alterar dados cadastrais da oficina nao deve alterar historico de movimentacoes, ordens de servico ou usuarios.
- Regras operacionais alteradas passam a valer apenas para novas acoes.
- Campos sensiveis de plano podem ser consultados, mas nao alterados diretamente pelo usuario.
- Configuracoes devem ter valores padrao ao criar uma nova oficina.

## Dados De Entrada

- `tenantId`.
- `userId`.
- `name`.
- `legalName`.
- `document`.
- `phone`.
- `email`.
- `address`.
- `timezone`.
- `currency`.
- `allowNegativeStock`.
- `autoDeductStockOnServiceOrderFinish`.
- `minimumStockDefault`.
- `notifyMinimumStock`.
- `notifyZeroStock`.
- `notificationEmail`.
- `notificationPhone`.

## Dados De Saida

- `tenantId`.
- Dados atualizados da oficina.
- Configuracoes operacionais atuais.
- Configuracoes de notificacao atuais.
- Plano atual em modo consulta.
- Data da ultima atualizacao.

## Resultado Esperado

Configuracoes da oficina atualizadas com seguranca, respeitando permissoes, tenant e limites do plano contratado.


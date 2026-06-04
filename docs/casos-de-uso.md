# AutoEstoque - Documento de Casos de Uso

## 1. Visao Geral

O AutoEstoque e uma plataforma SaaS com aplicativo movel para controle de estoque de oficinas mecanicas de pequeno e medio porte.

O objetivo do sistema e substituir controles manuais feitos em planilhas, cadernos ou sistemas genericos, oferecendo uma solucao simples para cadastro de pecas, movimentacoes de estoque, alertas de reposicao e integracao com ordens de servico.

## 2. Objetivos Dos Casos De Uso

Este documento descreve os principais casos de uso do AutoEstoque, considerando o escopo do MVP e a evolucao planejada para as proximas fases do produto.

Os casos de uso ajudam a definir:

- Quem utiliza o sistema.
- Quais acoes cada usuario pode executar.
- Quais regras o sistema deve aplicar.
- Quais fluxos sao essenciais para validar o produto.

## 3. Atores

### 3.1 Proprietario/Gerente

Responsavel pela administracao da oficina, usuarios, estoque, indicadores e tomada de decisao.

Principais interesses:

- Acompanhar valor e quantidade de estoque.
- Evitar compras duplicadas.
- Identificar pecas abaixo do minimo.
- Consultar historico de consumo.
- Gerenciar usuarios.

### 3.2 Responsavel Administrativo

Usuario responsavel pela operacao administrativa do estoque.

Principais interesses:

- Cadastrar produtos.
- Registrar entradas.
- Registrar ajustes.
- Consultar saldo.
- Acompanhar alertas.

### 3.3 Mecanico/Operador

Usuario responsavel pelo uso das pecas durante a execucao dos servicos.

Principais interesses:

- Consultar disponibilidade de pecas.
- Registrar consumo.
- Vincular pecas a ordens de servico.
- Finalizar servicos com baixa automatica.

### 3.4 Sistema

Ator automatico responsavel por executar regras internas.

Principais responsabilidades:

- Atualizar saldo de estoque.
- Gerar alertas.
- Baixar estoque ao finalizar ordem de servico.
- Registrar historico de movimentacoes.

### 3.5 Cliente Da Oficina

Ator indireto, representado no sistema por meio do cadastro de veiculos e ordens de servico.

## 4. Lista Geral De Casos De Uso

| Codigo | Caso de uso | Ator principal | Prioridade | Fase |
| --- | --- | --- | --- | --- |
| UC01 | Autenticar usuario | Usuario | Alta | MVP |
| UC02 | Recuperar senha | Usuario | Alta | MVP |
| UC03 | Gerenciar usuarios da oficina | Proprietario/Gerente | Alta | MVP |
| UC04 | Cadastrar produto/peca | Administrativo | Alta | MVP |
| UC05 | Editar produto/peca | Administrativo | Alta | MVP |
| UC06 | Consultar estoque | Administrativo/Mecanico | Alta | MVP |
| UC07 | Registrar entrada de estoque | Administrativo | Alta | MVP |
| UC08 | Registrar saida de estoque | Administrativo/Mecanico | Alta | MVP |
| UC09 | Registrar ajuste manual | Administrativo/Gerente | Alta | MVP |
| UC10 | Gerar alerta de estoque minimo | Sistema | Alta | MVP |
| UC11 | Gerar alerta de estoque zerado | Sistema | Alta | MVP |
| UC12 | Visualizar dashboard | Gerente | Alta | MVP |
| UC13 | Cadastrar veiculo | Administrativo | Media | Fase 2 |
| UC14 | Criar ordem de servico | Administrativo/Mecanico | Media | Fase 2 |
| UC15 | Adicionar pecas a ordem de servico | Mecanico | Media | Fase 2 |
| UC16 | Finalizar ordem de servico com baixa automatica | Mecanico/Sistema | Alta | Fase 2 |
| UC17 | Consultar historico de movimentacoes | Gerente/Administrativo | Alta | MVP |
| UC18 | Consultar produtos mais consumidos | Gerente | Media | MVP/Fase 3 |
| UC19 | Gerenciar configuracoes da oficina | Proprietario/Gerente | Media | MVP/Fase 2 |

## 5. Casos De Uso Detalhados

## UC01 - Autenticar Usuario

### Objetivo

Permitir que um usuario autorizado acesse o sistema da oficina.

### Ator Principal

Usuario.

### Pre-condicoes

- Usuario cadastrado.
- Usuario vinculado a uma oficina.
- Conta ativa.

### Fluxo Principal

1. Usuario acessa a tela de login.
2. Usuario informa e-mail e senha.
3. Sistema valida as credenciais.
4. Sistema identifica a oficina vinculada ao usuario.
5. Sistema cria a sessao de acesso.
6. Usuario e direcionado ao dashboard.

### Fluxos Alternativos

- Credenciais invalidas: sistema informa erro de autenticacao.
- Conta inativa: sistema bloqueia acesso e informa a situacao.
- Usuario vinculado a mais de uma empresa: sistema solicita a escolha da oficina.

### Resultado Esperado

Usuario autenticado e com acesso restrito aos dados da sua oficina.

## UC03 - Gerenciar Usuarios Da Oficina

### Objetivo

Permitir que o proprietario ou gerente cadastre e administre usuarios da oficina.

### Ator Principal

Proprietario/Gerente.

### Pre-condicoes

- Usuario autenticado.
- Usuario com permissao administrativa.
- Oficina cadastrada.

### Fluxo Principal

1. Gerente acessa o modulo de usuarios.
2. Sistema exibe os usuarios cadastrados na oficina.
3. Gerente clica em novo usuario.
4. Gerente informa nome, e-mail, perfil e status.
5. Sistema valida os dados.
6. Sistema cria o usuario vinculado a oficina.
7. Sistema envia orientacao de acesso ou convite, conforme regra definida.

### Fluxos Alternativos

- E-mail ja cadastrado: sistema informa que o usuario ja existe.
- Limite do plano atingido: sistema bloqueia novo cadastro ou sugere upgrade.

### Resultado Esperado

Usuario criado e vinculado corretamente ao tenant da oficina.

## UC04 - Cadastrar Produto/Peca

### Objetivo

Permitir que a oficina registre uma peca no estoque.

### Ator Principal

Responsavel Administrativo.

### Pre-condicoes

- Usuario autenticado.
- Usuario pertence a uma oficina.
- Usuario possui permissao para gerenciar produtos.

### Fluxo Principal

1. Usuario acessa o modulo de produtos.
2. Usuario clica em novo produto.
3. Usuario informa nome, SKU, codigo de barras, categoria, marca, fornecedor, estoque minimo e custo.
4. Sistema valida os campos obrigatorios.
5. Sistema verifica se SKU ou codigo de barras ja existem na mesma oficina.
6. Sistema salva o produto.
7. Produto fica disponivel para consulta e movimentacoes.

### Fluxos Alternativos

- SKU ja cadastrado: sistema bloqueia o cadastro e informa o conflito.
- Codigo de barras vazio: sistema permite salvar, caso o campo nao seja obrigatorio.
- Estoque minimo vazio: sistema sugere zero ou exige preenchimento, conforme regra definida.

### Resultado Esperado

Produto cadastrado no tenant correto e disponivel para movimentacao.

## UC05 - Editar Produto/Peca

### Objetivo

Permitir a atualizacao dos dados cadastrais de uma peca.

### Ator Principal

Responsavel Administrativo.

### Pre-condicoes

- Usuario autenticado.
- Produto cadastrado.
- Usuario com permissao para editar produtos.

### Fluxo Principal

1. Usuario acessa a lista de produtos.
2. Usuario seleciona um produto.
3. Sistema exibe os dados atuais.
4. Usuario altera as informacoes desejadas.
5. Sistema valida os dados.
6. Sistema salva as alteracoes.

### Fluxos Alternativos

- SKU alterado para um codigo ja existente: sistema bloqueia a alteracao.
- Produto com movimentacoes: sistema permite alterar dados cadastrais, mas nao remove historico.

### Resultado Esperado

Produto atualizado sem perda de historico.

## UC06 - Consultar Estoque

### Objetivo

Permitir a consulta rapida de produtos, saldos e situacao de estoque.

### Ator Principal

Administrativo ou Mecanico.

### Pre-condicoes

- Usuario autenticado.
- Existencia de produtos cadastrados.

### Fluxo Principal

1. Usuario acessa o modulo de estoque ou produtos.
2. Sistema exibe lista de pecas cadastradas.
3. Usuario pesquisa por nome, SKU, categoria, marca ou codigo de barras.
4. Sistema filtra os resultados.
5. Usuario visualiza estoque atual, estoque minimo, custo e status.

### Fluxos Alternativos

- Nenhum produto encontrado: sistema informa que nao ha resultados.
- Produto com estoque zerado: sistema exibe status de alerta.
- Produto abaixo do minimo: sistema destaca necessidade de reposicao.

### Resultado Esperado

Usuario identifica rapidamente a disponibilidade das pecas.

## UC07 - Registrar Entrada De Estoque

### Objetivo

Aumentar a quantidade disponivel de uma peca.

### Ator Principal

Responsavel Administrativo.

### Pre-condicoes

- Produto cadastrado.
- Usuario autenticado.
- Usuario com permissao para movimentar estoque.

### Fluxo Principal

1. Usuario acessa o produto ou o modulo de movimentacoes.
2. Usuario seleciona o tipo de entrada: compra, ajuste manual ou devolucao.
3. Usuario seleciona o produto.
4. Usuario informa quantidade, custo, motivo e observacao, quando aplicavel.
5. Sistema valida se a quantidade e maior que zero.
6. Sistema registra a movimentacao.
7. Sistema atualiza o estoque atual do produto.
8. Sistema remove ou atualiza alertas de estoque, caso o saldo tenha sido regularizado.

### Fluxos Alternativos

- Quantidade invalida: sistema bloqueia o registro.
- Produto inexistente: sistema solicita a selecao de um produto valido.
- Custo nao informado: sistema utiliza o custo atual do cadastro, se permitido.

### Resultado Esperado

Estoque atualizado e movimentacao registrada com usuario, data e motivo.

## UC08 - Registrar Saida De Estoque

### Objetivo

Reduzir o estoque de uma peca por consumo, perda, quebra ou ajuste.

### Ator Principal

Administrativo ou Mecanico.

### Pre-condicoes

- Produto cadastrado.
- Usuario autenticado.
- Estoque disponivel, exceto quando a regra permitir estoque negativo.

### Fluxo Principal

1. Usuario acessa o modulo de movimentacoes.
2. Usuario seleciona o tipo de saida: consumo em servico, perda, quebra ou ajuste manual.
3. Usuario seleciona o produto.
4. Usuario informa quantidade e motivo.
5. Sistema valida se ha saldo suficiente.
6. Sistema registra a movimentacao.
7. Sistema reduz o estoque atual.
8. Sistema verifica se o estoque ficou zerado ou abaixo do minimo.
9. Sistema gera alerta, se necessario.

### Fluxos Alternativos

- Estoque insuficiente: sistema bloqueia a saida ou solicita permissao especial.
- Produto sem estoque: sistema impede a baixa comum e sugere ajuste autorizado.
- Motivo nao informado: sistema bloqueia o registro.

### Resultado Esperado

Estoque reduzido e historico de saida registrado.

## UC09 - Registrar Ajuste Manual

### Objetivo

Corrigir diferencas de estoque identificadas em inventarios ou conferencias.

### Ator Principal

Administrativo ou Gerente.

### Pre-condicoes

- Usuario autenticado.
- Produto cadastrado.
- Usuario com permissao para ajuste manual.

### Fluxo Principal

1. Usuario acessa o modulo de movimentacoes.
2. Usuario seleciona ajuste manual.
3. Usuario escolhe se o ajuste sera de entrada ou saida.
4. Usuario seleciona o produto.
5. Usuario informa quantidade e motivo.
6. Sistema registra a movimentacao.
7. Sistema atualiza o saldo do produto.

### Fluxos Alternativos

- Usuario sem permissao: sistema bloqueia a operacao.
- Motivo vazio: sistema exige justificativa.

### Resultado Esperado

Saldo corrigido com rastreabilidade da alteracao.

## UC10 - Gerar Alerta De Estoque Minimo

### Objetivo

Avisar quando uma peca precisa de reposicao.

### Ator Principal

Sistema.

### Pre-condicoes

- Produto possui estoque minimo configurado.
- Estoque atual esta menor ou igual ao estoque minimo.

### Fluxo Principal

1. Sistema identifica alteracao no saldo do produto.
2. Sistema compara estoque atual com estoque minimo.
3. Caso esteja abaixo ou igual ao minimo, sistema cria ou atualiza um alerta.
4. Alerta aparece no dashboard.
5. Alerta fica disponivel para notificacao no aplicativo.

### Fluxos Alternativos

- Produto sem estoque minimo configurado: sistema nao gera alerta de minimo.
- Alerta ja existente: sistema atualiza a situacao em vez de duplicar o alerta.

### Resultado Esperado

Oficina visualiza itens que precisam de reposicao.

## UC11 - Gerar Alerta De Estoque Zerado

### Objetivo

Avisar quando uma peca nao possui saldo disponivel.

### Ator Principal

Sistema.

### Pre-condicoes

- Produto cadastrado.
- Estoque atual igual a zero.

### Fluxo Principal

1. Sistema identifica alteracao no saldo.
2. Sistema verifica que o estoque atual e zero.
3. Sistema cria ou atualiza alerta de estoque zerado.
4. Sistema exibe o alerta no dashboard.
5. Sistema disponibiliza o alerta para notificacao mobile.

### Fluxos Alternativos

- Produto recebe nova entrada: sistema remove ou marca o alerta como resolvido.

### Resultado Esperado

Oficina identifica pecas indisponiveis rapidamente.

## UC12 - Visualizar Dashboard

### Objetivo

Permitir que o gerente acompanhe os principais indicadores do estoque.

### Ator Principal

Proprietario/Gerente.

### Pre-condicoes

- Usuario autenticado.
- Usuario com acesso ao dashboard.

### Fluxo Principal

1. Usuario acessa o dashboard.
2. Sistema calcula os indicadores da oficina.
3. Sistema exibe quantidade total de produtos.
4. Sistema exibe produtos abaixo do minimo.
5. Sistema exibe valor total em estoque.
6. Sistema exibe produtos mais consumidos.
7. Sistema exibe movimentacoes do dia.

### Fluxos Alternativos

- Sem produtos cadastrados: sistema exibe indicadores zerados.
- Sem movimentacoes no dia: sistema exibe lista vazia.

### Resultado Esperado

Gerente acompanha a situacao do estoque em tempo real.

## UC13 - Cadastrar Veiculo

### Objetivo

Registrar os veiculos atendidos pela oficina.

### Ator Principal

Responsavel Administrativo.

### Pre-condicoes

- Usuario autenticado.
- Usuario com permissao para gerenciar veiculos.

### Fluxo Principal

1. Usuario acessa o modulo de veiculos.
2. Usuario clica em novo veiculo.
3. Usuario informa placa, marca, modelo, ano, proprietario e telefone.
4. Sistema valida os dados.
5. Sistema salva o veiculo vinculado a oficina.

### Fluxos Alternativos

- Placa ja cadastrada: sistema informa que o veiculo ja existe.
- Dados incompletos: sistema solicita preenchimento dos campos obrigatorios.

### Resultado Esperado

Veiculo cadastrado e disponivel para ordens de servico.

## UC14 - Criar Ordem De Servico

### Objetivo

Registrar um servico executado em um veiculo.

### Ator Principal

Administrativo ou Mecanico.

### Pre-condicoes

- Veiculo cadastrado.
- Usuario autenticado.

### Fluxo Principal

1. Usuario acessa o modulo de ordens de servico.
2. Usuario seleciona ou cadastra um veiculo.
3. Usuario informa cliente, servicos realizados e observacoes.
4. Sistema salva a ordem de servico em aberto.
5. Sistema vincula a ordem de servico a oficina.

### Fluxos Alternativos

- Veiculo nao cadastrado: sistema permite cadastrar antes de criar a ordem.
- Dados obrigatorios ausentes: sistema solicita correcao.

### Resultado Esperado

Ordem de servico criada e disponivel para inclusao de pecas.

## UC15 - Adicionar Pecas A Ordem De Servico

### Objetivo

Vincular pecas utilizadas a uma ordem de servico.

### Ator Principal

Mecanico.

### Pre-condicoes

- Ordem de servico aberta.
- Produto cadastrado.
- Usuario autenticado.

### Fluxo Principal

1. Usuario acessa uma ordem de servico aberta.
2. Usuario seleciona a opcao de adicionar peca.
3. Usuario pesquisa e seleciona o produto.
4. Usuario informa a quantidade utilizada.
5. Sistema valida disponibilidade.
6. Sistema adiciona a peca a ordem de servico.

### Fluxos Alternativos

- Estoque insuficiente: sistema alerta o usuario.
- Produto nao encontrado: sistema permite nova busca.

### Resultado Esperado

Pecas ficam vinculadas a ordem de servico para baixa posterior.

## UC16 - Finalizar Ordem De Servico Com Baixa Automatica

### Objetivo

Dar baixa automatica nas pecas usadas durante o servico.

### Ator Principal

Mecanico/Sistema.

### Pre-condicoes

- Ordem de servico aberta.
- Pecas adicionadas a ordem de servico.
- Pecas possuem estoque suficiente.

### Fluxo Principal

1. Usuario revisa a ordem de servico.
2. Usuario confirma servicos e pecas utilizadas.
3. Usuario clica em finalizar.
4. Sistema valida estoque das pecas.
5. Sistema registra uma saida para cada peca usada.
6. Sistema atualiza o estoque.
7. Sistema marca a ordem de servico como finalizada.
8. Sistema gera alertas de reposicao, se necessario.

### Fluxos Alternativos

- Peca sem estoque suficiente: sistema bloqueia a finalizacao ou solicita ajuste autorizado.
- Peca removida antes da finalizacao: sistema nao baixa o item removido.
- Falha durante baixa: sistema mantem a ordem em aberto e informa o erro.

### Resultado Esperado

Ordem de servico finalizada, estoque atualizado automaticamente e historico de consumo registrado.

## UC17 - Consultar Historico De Movimentacoes

### Objetivo

Permitir auditoria e rastreabilidade das entradas, saidas e ajustes de estoque.

### Ator Principal

Gerente ou Administrativo.

### Pre-condicoes

- Usuario autenticado.
- Usuario com permissao para consultar movimentacoes.

### Fluxo Principal

1. Usuario acessa o historico de movimentacoes.
2. Sistema exibe movimentacoes da oficina.
3. Usuario filtra por periodo, produto, tipo, motivo ou usuario.
4. Sistema atualiza a lista conforme os filtros.
5. Usuario visualiza data, produto, quantidade, tipo, motivo e responsavel.

### Fluxos Alternativos

- Nenhuma movimentacao encontrada: sistema exibe estado vazio.
- Usuario sem permissao: sistema bloqueia acesso.

### Resultado Esperado

Usuario consegue rastrear alteracoes de estoque.

## UC18 - Consultar Produtos Mais Consumidos

### Objetivo

Identificar quais pecas tem maior consumo na oficina.

### Ator Principal

Proprietario/Gerente.

### Pre-condicoes

- Usuario autenticado.
- Existencia de movimentacoes de saida.

### Fluxo Principal

1. Usuario acessa dashboard ou relatorio de consumo.
2. Sistema calcula o consumo por produto em um periodo.
3. Sistema ordena os produtos mais consumidos.
4. Usuario visualiza ranking de consumo.

### Fluxos Alternativos

- Sem movimentacoes no periodo: sistema exibe estado vazio.
- Periodo nao informado: sistema utiliza periodo padrao.

### Resultado Esperado

Gerente identifica pecas de maior giro e melhora o planejamento de compras.

## UC19 - Gerenciar Configuracoes Da Oficina

### Objetivo

Permitir que o proprietario ou gerente configure dados cadastrais, parametros operacionais e preferencias de notificacao da oficina.

### Ator Principal

Proprietario/Gerente.

### Pre-condicoes

- Usuario autenticado.
- Usuario vinculado a uma oficina.
- Usuario com permissao para administrar configuracoes.

### Fluxo Principal

1. Usuario acessa o modulo de configuracoes.
2. Sistema exibe as configuracoes atuais da oficina.
3. Usuario altera dados cadastrais, parametros operacionais ou preferencias de notificacao.
4. Sistema valida dados, permissoes e restricoes do plano.
5. Sistema salva as configuracoes no tenant da oficina.
6. Sistema informa que as configuracoes foram atualizadas.

### Fluxos Alternativos

- Usuario sem permissao: sistema bloqueia acesso.
- Dados invalidos: sistema informa campos que precisam ser corrigidos.
- Recurso indisponivel no plano: sistema bloqueia alteracao e informa restricao.

### Resultado Esperado

Oficina possui configuracoes atualizadas e aplicadas aos proximos fluxos operacionais do sistema.

## 6. Casos De Uso Essenciais Para O MVP

Para validar o produto nos primeiros 30 dias, os casos de uso mais importantes sao:

| Prioridade | Caso de uso | Justificativa |
| --- | --- | --- |
| 1 | Login e multiempresa | Base para separar dados de cada oficina |
| 2 | Cadastro de produtos | Estrutura inicial do estoque |
| 3 | Consulta de estoque | Valor imediato para a oficina |
| 4 | Entrada de estoque | Permite registrar compras e reposicoes |
| 5 | Saida de estoque | Permite controlar consumo e perdas |
| 6 | Historico de movimentacoes | Garante rastreabilidade |
| 7 | Alertas de estoque minimo e zerado | Reduz falta de pecas |
| 8 | Dashboard basico | Apoia decisao do gestor |

## 7. Escopo Sugerido Por Fase

### Fase 1 - MVP

- Autenticacao.
- Multiempresa.
- Cadastro de produtos.
- Movimentacoes de entrada.
- Movimentacoes de saida.
- Ajustes manuais.
- Alertas de estoque minimo.
- Alertas de estoque zerado.
- Dashboard basico.
- Historico de movimentacoes.

### Fase 2 - Operacao Da Oficina

- Cadastro de veiculos.
- Ordens de servico.
- Pecas utilizadas na ordem de servico.
- Baixa automatica ao finalizar ordem.
- Historico de consumo por veiculo/cliente.

### Fase 3 - Crescimento

- Relatorios avancados.
- Compras e fornecedores.
- Exportacao de dados.
- Integracoes.
- Notificacoes via WhatsApp.

## 8. Regras De Negocio Iniciais

- Cada oficina deve visualizar apenas seus proprios dados.
- Produto nao deve repetir SKU dentro da mesma oficina.
- Codigo de barras pode ser usado para busca rapida.
- Toda movimentacao deve registrar usuario, data, tipo, quantidade e motivo.
- Saidas de estoque devem validar saldo disponivel.
- Ajustes manuais devem exigir justificativa.
- Produtos abaixo do estoque minimo devem gerar alerta.
- Produtos com estoque zerado devem gerar alerta especifico.
- Finalizacao de ordem de servico deve gerar baixa automatica das pecas utilizadas.

## 9. Criterio De Sucesso Dos Casos De Uso

Os casos de uso do MVP serao considerados validados quando oficinas reais conseguirem:

- Cadastrar suas pecas principais.
- Registrar entradas e saidas sem depender de planilhas.
- Consultar saldo atualizado.
- Receber alertas de reposicao.
- Usar o historico para entender consumo e perdas.

O produto sera considerado validado quando pelo menos 5 oficinas estiverem pagando pelo servico e utilizando o sistema semanalmente para registrar movimentacoes reais de estoque.

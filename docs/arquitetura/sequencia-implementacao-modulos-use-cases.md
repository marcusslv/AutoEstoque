# AutoEstoque - Sequencia De Implementacao Dos Modulos E Casos De Uso

## 1. Objetivo

Este documento define a ordem recomendada para implementar os modulos do backend do AutoEstoque, considerando:

- Entrega incremental do MVP.
- Dependencias entre casos de uso.
- Clean Architecture.
- Domain-Driven Design.
- Modular Monolith.
- Uso de DTOs de Input e Output nos use cases.
- Uso de Presenters na camada de Interfaces.

A ideia e implementar o produto em fatias verticais. Cada fatia deve entregar um caso de uso funcional de ponta a ponta, passando por rota HTTP, validacao, use case, dominio, persistencia e resposta da API.

## 2. Principio De Implementacao

A sequencia recomendada nao deve seguir apenas a ordem numerica dos casos de uso.

O criterio principal deve ser:

1. Implementar primeiro os conceitos centrais do dominio.
2. Criar a menor base tecnica necessaria.
3. Evitar iniciar por funcionalidades administrativas complexas.
4. Validar a arquitetura com um fluxo simples e importante.
5. Evoluir para movimentacoes de estoque.
6. Depois integrar com ordens de servico e dashboard.

Por isso, o primeiro caso de uso de negocio recomendado e:

**UC04 - Cadastrar produto/peca**

Mesmo que autenticacao e multiempresa sejam importantes, iniciar por produto permite validar rapidamente a arquitetura e criar a base para os demais fluxos do estoque.

## 3. Modulos Do Backend

## 3.1 Identity & Access

Responsavel por autenticacao, usuarios, recuperacao de senha e permissoes.

Casos de uso:

- UC01 - Autenticar usuario.
- UC02 - Recuperar senha.
- UC03 - Gerenciar usuarios da oficina.

## 3.2 Tenant/Organization

Responsavel pela separacao multiempresa.

Conceitos principais:

- Oficina.
- Tenant.
- Vinculo entre usuario e oficina.
- Plano contratado.

No inicio, este modulo pode ser implementado de forma minima para permitir que os demais modulos ja salvem dados com `tenant_id`.

## 3.3 Settings/Tenant

Responsavel por configuracoes cadastrais, operacionais e preferencias da oficina.

Casos de uso:

- UC19 - Gerenciar configuracoes da oficina.

Este modulo depende de Tenant/Organization e Identity & Access para garantir que apenas usuarios autorizados alterem configuracoes do tenant atual.

## 3.4 Catalog

Responsavel pelo cadastro das pecas e dados cadastrais relacionados.

Casos de uso:

- UC04 - Cadastrar produto/peca.
- UC05 - Editar produto/peca.
- Parte do UC06 - Consultar estoque, quando envolver dados cadastrais do produto.

## 3.5 Inventory

Responsavel por saldo, movimentacoes e alertas de estoque.

Casos de uso:

- UC06 - Consultar estoque.
- UC07 - Registrar entrada de estoque.
- UC08 - Registrar saida de estoque.
- UC09 - Registrar ajuste manual.
- UC10 - Gerar alerta de estoque minimo.
- UC11 - Gerar alerta de estoque zerado.
- UC17 - Consultar historico de movimentacoes.
- UC18 - Consultar produtos mais consumidos.

## 3.6 Workshop Operations

Responsavel por veiculos e ordens de servico.

Casos de uso:

- UC13 - Cadastrar veiculo.
- UC14 - Criar ordem de servico.
- UC15 - Adicionar pecas a ordem de servico.
- UC16 - Finalizar ordem de servico com baixa automatica.

## 3.7 Dashboard/Reporting

Responsavel por indicadores e consultas agregadas.

Casos de uso:

- UC12 - Visualizar dashboard.
- UC18 - Consultar produtos mais consumidos.

Este modulo deve consultar dados dos outros modulos, mas nao deve concentrar regras de negocio de estoque.

## 4. Sequencia Recomendada De Implementacao

## Fase 0 - Fundacao Tecnica Do Backend

Esta fase prepara o projeto para receber os casos de uso.

Nao e um caso de uso de negocio, mas e importante para evitar retrabalho.

Entregas:

- Estrutura inicial de modulos.
- Convencao de namespaces.
- Padrao de pastas por modulo.
- Configuracao de rotas API.
- Base para DTOs de Input e Output.
- Base para Presenters.
- Base para repositories.
- Tenant minimo.

Implementacao sugerida:

- Criar estrutura `app/Modules`.
- Criar modulo `Tenant`.
- Criar uma forma temporaria de resolver o tenant atual.
- Usar header `X-Tenant-Id` enquanto autenticacao completa nao estiver pronta.
- Criar middleware ou service `TenantContext`.

Objetivo:

- Permitir que os demais casos de uso ja sejam implementados considerando multiempresa.

## Fase 1 - Catalogo De Produtos

Esta e a primeira fase de negocio.

## 1. UC04 - Cadastrar Produto/Peca

Modulo principal:

- Catalog.

Por que implementar primeiro:

- E base para estoque, movimentacoes, alertas, dashboard e ordens de servico.
- Valida a arquitetura com um fluxo simples.
- Permite criar o primeiro padrao completo de use case.

Componentes esperados:

- `CreateProductInput`.
- `CreateProductOutput`.
- `CreateProductUseCase`.
- `Product` como entidade ou aggregate.
- `ProductFactory` para criacao da entidade.
- `ProductValidator` para invariantes da entidade.
- Entidade abstrata compartilhada com `Notification` como atributo.
- `Notification` para acumular erros de dominio dentro da entidade.
- `DomainValidationException` para bloquear estado invalido.
- Value Objects como `Sku`, `Barcode` e `Money`, se fizer sentido.
- `ProductRepository`.
- Implementacao Eloquent do repository.
- Migration de produtos.
- Controller API.
- Request validation.
- Presenter.
- Testes de feature e/ou unidade.

Observacao importante:

- O campo `estoque_atual` nao deve ser tratado como propriedade principal do cadastro do produto.
- O saldo de estoque deve ser resultado das movimentacoes.
- Se for necessario para acelerar o MVP, pode existir uma coluna de saldo em Inventory, mas nao deve ser atualizada diretamente pelo cadastro do produto.

## 2. UC05 - Editar Produto/Peca

Modulo principal:

- Catalog.

Dependencia:

- UC04.

Objetivo:

- Permitir alteracao de dados cadastrais da peca.

Componentes esperados:

- `UpdateProductInput`.
- `UpdateProductOutput`.
- `UpdateProductUseCase`.
- Regras de unicidade de SKU por oficina.
- Presenter de produto atualizado.

## 3. UC06 - Consultar Estoque

Modulos envolvidos:

- Catalog.
- Inventory.

Dependencias:

- UC04.
- Fase inicial de Inventory, mesmo que ainda sem movimentacoes completas.

Objetivo:

- Permitir consulta de produtos e saldos.

Implementacao inicial:

- Listagem de produtos.
- Busca por nome, SKU ou codigo de barras.
- Retorno do saldo atual quando Inventory ja estiver disponivel.

Observacao:

- Este caso de uso pode comecar como uma consulta simples e evoluir depois que movimentacoes forem implementadas.

## Fase 2 - Movimentacoes De Estoque

Esta fase transforma o cadastro em controle real de estoque.

## 4. UC07 - Registrar Entrada De Estoque

Modulo principal:

- Inventory.

Dependencias:

- UC04.
- UC06 parcialmente implementado.

Objetivo:

- Registrar compras, devolucoes ou ajustes positivos.

Componentes esperados:

- `RegisterStockEntryInput`.
- `RegisterStockEntryOutput`.
- `RegisterStockEntryUseCase`.
- Entidade `StockMovement`.
- Controle de saldo por produto e tenant.
- Motivo obrigatorio.
- Usuario responsavel.
- Data da movimentacao.

Regras importantes:

- Quantidade deve ser maior que zero.
- Produto deve existir no tenant atual.
- Movimento deve atualizar saldo de forma transacional.

## 5. UC08 - Registrar Saida De Estoque

Modulo principal:

- Inventory.

Dependencias:

- UC04.
- UC07.

Objetivo:

- Registrar consumo, perda, quebra ou saida manual.

Componentes esperados:

- `RegisterStockOutputInput`.
- `RegisterStockOutputOutput`.
- `RegisterStockOutputUseCase`.
- Validacao de saldo disponivel.
- Registro do motivo.
- Atualizacao transacional do saldo.

Regras importantes:

- Nao permitir saldo negativo, salvo se uma regra futura permitir.
- Registrar usuario, data e motivo.
- Preparar integracao futura com ordem de servico.

## 6. UC09 - Registrar Ajuste Manual

Modulo principal:

- Inventory.

Dependencias:

- UC07.
- UC08.

Objetivo:

- Corrigir divergencias de estoque com rastreabilidade.

Componentes esperados:

- `RegisterStockAdjustmentInput`.
- `RegisterStockAdjustmentOutput`.
- `RegisterStockAdjustmentUseCase`.
- Motivo obrigatorio.
- Diferenca entre saldo anterior e novo saldo.

Regras importantes:

- Todo ajuste deve gerar movimentacao.
- Ajuste nao deve apagar historico anterior.

## 7. UC17 - Consultar Historico De Movimentacoes

Modulo principal:

- Inventory.

Dependencias:

- UC07.
- UC08.
- UC09.

Objetivo:

- Permitir rastrear entradas, saidas e ajustes.

Implementacao sugerida:

- Filtros por produto.
- Filtros por periodo.
- Filtros por tipo de movimentacao.
- Filtros por usuario.

## Fase 3 - Alertas E Indicadores Basicos

Esta fase usa dados ja existentes para gerar valor operacional.

## 8. UC10 - Gerar Alerta De Estoque Minimo

Modulo principal:

- Inventory.

Dependencias:

- UC04.
- UC07.
- UC08.
- UC09.

Objetivo:

- Identificar produtos abaixo do estoque minimo.

Implementacao sugerida:

- Alerta calculado inicialmente sob demanda.
- Depois pode evoluir para eventos, jobs ou notificacoes.

## 9. UC11 - Gerar Alerta De Estoque Zerado

Modulo principal:

- Inventory.

Dependencias:

- UC04.
- UC07.
- UC08.
- UC09.

Objetivo:

- Identificar produtos com saldo igual a zero.

Implementacao sugerida:

- Pode compartilhar parte da consulta com UC10.
- Pode gerar um tipo diferente de alerta.

## 10. UC12 - Visualizar Dashboard

Modulo principal:

- Dashboard/Reporting.

Dependencias:

- UC04.
- UC06.
- UC07.
- UC08.
- UC10.
- UC11.

Objetivo:

- Exibir indicadores principais do MVP.

Indicadores iniciais:

- Quantidade total de produtos.
- Produtos abaixo do minimo.
- Produtos zerados.
- Valor total em estoque.
- Movimentacoes do dia.

Observacao:

- Este modulo deve usar consultas otimizadas.
- Nao deve conter regras centrais de movimentacao de estoque.

## 11. UC18 - Consultar Produtos Mais Consumidos

Modulo principal:

- Dashboard/Reporting.
- Inventory.

Dependencias:

- UC08.
- UC17.

Objetivo:

- Identificar os produtos com maior consumo em um periodo.

Implementacao sugerida:

- Consulta agregada sobre movimentacoes de saida.
- Filtro por periodo.
- Ordenacao por quantidade consumida.

## Fase 4 - Identidade, Acesso E Multiempresa Completo

Esta fase formaliza o uso real por oficinas e usuarios.

Ela pode ser antecipada se o backend precisar ficar exposto para usuarios reais antes das funcionalidades de estoque estarem maduras.

## 12. UC01 - Autenticar Usuario

Modulo principal:

- Identity & Access.

Dependencias:

- Tenant minimo.

Objetivo:

- Permitir login seguro na API.

Implementacao sugerida:

- Login por email e senha.
- Token de API.
- Vinculo do usuario com tenant/oficina.
- Resolucao automatica do tenant apos login.

## 13. UC02 - Recuperar Senha

Modulo principal:

- Identity & Access.

Dependencias:

- UC01.

Objetivo:

- Permitir recuperacao de acesso.

Implementacao sugerida:

- Fluxo padrao do Laravel.
- Envio de email.
- Token temporario.

## 14. UC03 - Gerenciar Usuarios Da Oficina

Modulo principal:

- Identity & Access.
- Tenant/Organization.

Dependencias:

- UC01.
- Tenant/Organization.

Objetivo:

- Permitir que o proprietario ou gerente gerencie usuarios da oficina.

Implementacao sugerida:

- Criar usuario.
- Editar usuario.
- Desativar usuario.
- Definir papel/permissao.

## 15. UC19 - Gerenciar Configuracoes Da Oficina

Modulo principal:

- Settings/Tenant.

Dependencias:

- UC01.
- UC03.
- Tenant/Organization.

Objetivo:

- Permitir que o proprietario ou gerente configure dados cadastrais, parametros operacionais e preferencias da oficina.

Componentes esperados:

- `GetWorkshopSettingsInput`.
- `GetWorkshopSettingsOutput`.
- `GetWorkshopSettingsUseCase`.
- `UpdateWorkshopSettingsInput`.
- `UpdateWorkshopSettingsOutput`.
- `UpdateWorkshopSettingsUseCase`.

Regras importantes:

- Configuracoes devem pertencer ao tenant atual.
- Apenas perfis administrativos podem alterar configuracoes.
- Dados de plano podem ser consultados, mas nao alterados diretamente.
- Alteracoes operacionais devem valer apenas para novas acoes.
- Deve haver configuracoes padrao ao criar uma nova oficina.

## Fase 5 - Operacao Da Oficina

Esta fase conecta estoque ao fluxo operacional da oficina.

## 16. UC13 - Cadastrar Veiculo

Modulo principal:

- Workshop Operations.

Dependencias:

- Tenant completo ou tenant minimo.

Objetivo:

- Registrar veiculos atendidos pela oficina.

Componentes esperados:

- `CreateVehicleInput`.
- `CreateVehicleOutput`.
- `CreateVehicleUseCase`.
- Dados de proprietario e telefone.

## 17. UC14 - Criar Ordem De Servico

Modulo principal:

- Workshop Operations.

Dependencias:

- UC13.
- UC01, se autenticacao ja estiver ativa.

Objetivo:

- Criar uma ordem de servico vinculada a um veiculo e cliente.

Componentes esperados:

- `CreateServiceOrderInput`.
- `CreateServiceOrderOutput`.
- `CreateServiceOrderUseCase`.
- Status inicial da ordem.

## 18. UC15 - Adicionar Pecas A Ordem De Servico

Modulos envolvidos:

- Workshop Operations.
- Catalog.
- Inventory.

Dependencias:

- UC04.
- UC06.
- UC14.

Objetivo:

- Informar quais pecas serao usadas na ordem de servico.

Observacao:

- Nesta etapa, a peca pode ser apenas reservada ou vinculada a ordem.
- A baixa definitiva deve acontecer no UC16.

## 19. UC16 - Finalizar Ordem De Servico Com Baixa Automatica

Modulos envolvidos:

- Workshop Operations.
- Inventory.

Dependencias:

- UC08.
- UC14.
- UC15.

Objetivo:

- Finalizar a ordem de servico e baixar automaticamente as pecas utilizadas.

Regras importantes:

- Validar saldo antes de finalizar.
- Gerar movimentacoes de saida.
- Manter rastreabilidade entre ordem de servico e movimentacoes.
- Executar o fluxo de forma transacional.

## 5. Ordem Resumida Recomendada

| Ordem | Modulo | Caso de uso | Motivo |
| --- | --- | --- | --- |
| 0 | Base tecnica | Tenant minimo e estrutura modular | Preparar backend para Clean Architecture, DDD e multiempresa |
| 1 | Catalog | UC04 - Cadastrar produto/peca | Primeiro caso de uso central do dominio |
| 2 | Catalog | UC05 - Editar produto/peca | Completa manutencao cadastral |
| 3 | Catalog/Inventory | UC06 - Consultar estoque | Permite visualizar produtos e saldos |
| 4 | Inventory | UC07 - Registrar entrada de estoque | Inicia controle real de estoque |
| 5 | Inventory | UC08 - Registrar saida de estoque | Completa fluxo basico de movimentacao |
| 6 | Inventory | UC09 - Registrar ajuste manual | Permite correcao com rastreabilidade |
| 7 | Inventory | UC17 - Consultar historico de movimentacoes | Da auditoria ao estoque |
| 8 | Inventory | UC10 - Gerar alerta de estoque minimo | Gera valor operacional |
| 9 | Inventory | UC11 - Gerar alerta de estoque zerado | Gera valor operacional |
| 10 | Dashboard/Reporting | UC12 - Visualizar dashboard | Consolida indicadores do MVP |
| 11 | Dashboard/Reporting | UC18 - Consultar produtos mais consumidos | Ajuda em decisao de compra |
| 12 | Identity & Access | UC01 - Autenticar usuario | Formaliza acesso real ao sistema |
| 13 | Identity & Access | UC02 - Recuperar senha | Completa fluxo basico de acesso |
| 14 | Identity & Access | UC03 - Gerenciar usuarios da oficina | Permite operacao multiusuario |
| 15 | Settings/Tenant | UC19 - Gerenciar configuracoes da oficina | Centraliza preferencias operacionais do tenant |
| 16 | Workshop Operations | UC13 - Cadastrar veiculo | Inicia modulo operacional da oficina |
| 17 | Workshop Operations | UC14 - Criar ordem de servico | Cria fluxo de servicos |
| 18 | Workshop Operations | UC15 - Adicionar pecas a ordem de servico | Integra OS com catalogo |
| 19 | Workshop Operations/Inventory | UC16 - Finalizar OS com baixa automatica | Entrega diferencial principal do produto |

## 6. Ordem Alternativa Para MVP Comercial

Se o objetivo for disponibilizar a aplicacao para usuarios reais o quanto antes, a ordem pode ser ajustada para antecipar autenticacao.

Ordem alternativa:

1. Base tecnica com Tenant minimo.
2. UC01 - Autenticar usuario.
3. UC03 - Gerenciar usuarios da oficina.
4. UC04 - Cadastrar produto/peca.
5. UC05 - Editar produto/peca.
6. UC06 - Consultar estoque.
7. UC07 - Registrar entrada de estoque.
8. UC08 - Registrar saida de estoque.
9. UC10 - Gerar alerta de estoque minimo.
10. UC11 - Gerar alerta de estoque zerado.
11. UC12 - Visualizar dashboard.

Essa ordem e mais segura para ambiente real, mas atrasa um pouco a validacao do dominio de estoque.

## 7. Recomendacao Final

Para o estado atual do projeto, a melhor decisao e implementar primeiro:

**Fase 0 - Fundacao Tecnica Do Backend**

Em seguida:

**UC04 - Cadastrar Produto/Peca**

Esse caminho permite criar um exemplo real da arquitetura e usar esse padrao nos demais casos de uso.

Depois do UC04, a sequencia natural deve ser:

1. UC05 - Editar produto/peca.
2. UC06 - Consultar estoque.
3. UC07 - Registrar entrada de estoque.
4. UC08 - Registrar saida de estoque.
5. UC09 - Registrar ajuste manual.
6. UC17 - Consultar historico de movimentacoes.
7. UC10 e UC11 - Alertas.
8. UC12 - Dashboard.

Essa sequencia entrega valor progressivo e evita construir telas ou fluxos que ainda nao possuem dados reais suficientes.

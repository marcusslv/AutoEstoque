# AutoEstoque Backend - Casos De Uso E Regras De Negocio

## Objetivo

Este documento consolida os casos de uso do backend do AutoEstoque e suas principais regras de negocio.

Ele serve como referencia para implementacao, manutencao, testes e validacao do contrato da API.

## Convencoes Gerais

Todas as regras abaixo devem respeitar:

- multiempresa por `tenant_id`;
- usuario autenticado quando o endpoint for protegido;
- autorizacao por perfil;
- respostas JSON padronizadas;
- validacao de entrada por FormRequest;
- execucao de regra de aplicacao em UseCase;
- saida por Output DTO e Presenter.

## Perfis

Perfis administrativos:

- `owner`;
- `manager`;
- `admin`.

Perfil operacional:

- `mechanic`.

Regra geral:

- `owner`, `manager` e `admin` acessam backoffice.
- `mechanic` acessa operacoes da oficina, mas nao administra usuarios, configuracoes, produtos ou movimentacoes manuais.

## UC01 - Autenticar Usuario

Modulo:

- Identity.

Endpoint:

```text
POST /api/v1/auth/login
```

Objetivo:

- Permitir que um usuario ativo acesse a API.

Regras de negocio:

- E-mail e senha sao obrigatorios.
- Usuario deve existir.
- Usuario deve estar ativo.
- Usuario deve estar vinculado a um tenant.
- Credenciais invalidas retornam erro de autenticacao.
- Login bem-sucedido emite token Bearer.
- Resposta deve retornar usuario autenticado e dados do token.
- Tenant passa a ser resolvido pelo token, nao por header manual.

## UC02 - Recuperar Senha

Modulo:

- Identity.

Endpoints:

```text
POST /api/v1/auth/forgot-password
POST /api/v1/auth/reset-password
```

Objetivo:

- Permitir que usuario solicite redefinicao de senha.

Regras de negocio:

- E-mail deve ser valido.
- Solicitar recuperacao nao deve expor se o e-mail existe ou nao.
- Token de redefinicao deve ser validado.
- Nova senha deve respeitar validacao definida no backend.
- Confirmacao de senha deve coincidir.

## UC03 - Gerenciar Usuarios Da Oficina

Modulo:

- Identity.

Endpoints:

```text
GET /api/v1/users
POST /api/v1/users
PATCH /api/v1/users/{user}
PATCH /api/v1/users/{user}/deactivate
```

Objetivo:

- Permitir que perfis administrativos gerenciem usuarios do tenant atual.

Regras de negocio:

- Apenas `owner`, `manager` e `admin` podem acessar.
- Usuario listado deve pertencer ao tenant atual.
- Usuario criado deve ser vinculado ao tenant atual.
- E-mail deve ser unico.
- E-mail deve ser normalizado para minusculo.
- Usuario ativo conta para limite do plano.
- Plano Starter permite ate 3 usuarios ativos.
- Usuario de outro tenant nao pode ser editado.
- Inativar usuario bloqueia novos acessos.
- Perfil deve ser um dos valores permitidos: `owner`, `manager`, `admin`, `mechanic`.

## UC04 - Cadastrar Produto/Peca

Modulo:

- Catalog.

Endpoint:

```text
POST /api/v1/products
```

Objetivo:

- Cadastrar uma peca/produto no catalogo da oficina.

Regras de negocio:

- Apenas perfis administrativos podem cadastrar.
- Produto deve pertencer ao tenant atual.
- Nome e SKU sao obrigatorios.
- SKU deve ser unico dentro do tenant.
- Codigo de barras, quando informado, deve ser valido.
- Codigo de barras nao deve duplicar outro produto do tenant.
- Estoque minimo nao pode ser negativo.
- Custo nao pode ser negativo.
- Moeda padrao e `BRL`.
- Cadastro de produto nao deve alterar saldo diretamente.
- Saldo deve ser controlado pelo modulo Inventory.

## UC05 - Editar Produto/Peca

Modulo:

- Catalog.

Endpoint:

```text
PATCH /api/v1/products/{product}
```

Objetivo:

- Atualizar dados cadastrais da peca/produto.

Regras de negocio:

- Apenas perfis administrativos podem editar.
- Produto deve pertencer ao tenant atual.
- Produto de outro tenant deve retornar nao encontrado.
- SKU atualizado deve continuar unico no tenant.
- Codigo de barras atualizado deve continuar unico no tenant.
- Historico de movimentacoes nao deve ser alterado.
- Alterar custo cadastral nao recalcula movimentacoes antigas.

## UC06 - Consultar Estoque

Modulo:

- Catalog/Inventory.

Endpoint:

```text
GET /api/v1/stock
```

Objetivo:

- Consultar produtos com saldo atual.

Regras de negocio:

- Perfis `owner`, `manager`, `admin` e `mechanic` podem consultar.
- Consulta deve retornar apenas produtos do tenant atual.
- Busca pode filtrar por termo.
- Saldo exibido vem de Inventory.
- Produto sem item de estoque deve ser exibido com saldo zero.
- Status de estoque:
  - `available`: saldo maior ou igual ao minimo;
  - `below_minimum`: saldo maior que zero e menor que minimo;
  - `zero`: saldo igual a zero.

## UC07 - Registrar Entrada De Estoque

Modulo:

- Inventory.

Endpoint:

```text
POST /api/v1/inventory/entries
```

Objetivo:

- Registrar entrada de pecas no estoque.

Regras de negocio:

- Apenas perfis administrativos podem registrar.
- Produto deve existir no tenant atual.
- Tipo permitido: `purchase`, `manual_adjustment`, `return`.
- Quantidade deve ser maior que zero.
- Motivo e obrigatorio.
- Entrada deve gerar movimentacao.
- Entrada deve aumentar saldo do item de estoque.
- Usuario autenticado deve ser registrado na movimentacao.
- Data da movimentacao deve ser registrada.
- Custo unitario pode ser informado em entradas.

## UC08 - Registrar Saida De Estoque

Modulo:

- Inventory.

Endpoint:

```text
POST /api/v1/inventory/outputs
```

Objetivo:

- Registrar saida manual de pecas do estoque.

Regras de negocio:

- Apenas perfis administrativos podem registrar saida manual.
- Produto deve existir no tenant atual.
- Tipo permitido: `service_consumption`, `loss`, `breakage`, `manual_adjustment`.
- Quantidade deve ser maior que zero.
- Motivo e obrigatorio.
- Saida deve gerar movimentacao.
- Saida deve reduzir saldo.
- Saida nao deve permitir saldo negativo, salvo regra operacional futura.
- Usuario autenticado deve ser registrado.

## UC09 - Registrar Ajuste Manual

Modulo:

- Inventory.

Endpoint:

```text
POST /api/v1/inventory/adjustments
```

Objetivo:

- Corrigir saldo de estoque com rastreabilidade.

Regras de negocio:

- Apenas perfis administrativos podem ajustar.
- Produto deve existir no tenant atual.
- Direcao deve ser `entry` ou `output`.
- Quantidade deve ser maior que zero.
- Motivo e obrigatorio.
- Ajuste deve gerar movimentacao.
- Ajuste de entrada aumenta saldo.
- Ajuste de saida reduz saldo.
- Ajuste de saida deve validar saldo disponivel.

## UC10 - Gerar Alerta De Estoque Minimo

Modulo:

- Inventory.

Endpoint:

```text
GET /api/v1/inventory/alerts/minimum-stock
```

Objetivo:

- Listar produtos abaixo do estoque minimo.

Regras de negocio:

- Apenas perfis administrativos podem consultar.
- Alertas devem considerar apenas tenant atual.
- Produto com saldo maior que zero e menor que minimo deve aparecer.
- Produto zerado deve ser tratado pelo alerta de estoque zerado.
- Resposta deve retornar quantidade faltante (`shortage_quantity`).
- Limite de retorno deve respeitar validacao da API.

## UC11 - Gerar Alerta De Estoque Zerado

Modulo:

- Inventory.

Endpoint:

```text
GET /api/v1/inventory/alerts/zero-stock
```

Objetivo:

- Listar produtos com estoque zerado.

Regras de negocio:

- Apenas perfis administrativos podem consultar.
- Alertas devem considerar apenas tenant atual.
- Produto com saldo igual a zero deve aparecer.
- Resposta deve informar produto, saldo atual e estoque minimo.
- Limite de retorno deve respeitar validacao da API.

## UC12 - Visualizar Dashboard

Modulo:

- Dashboard.

Endpoint:

```text
GET /api/v1/dashboard
```

Objetivo:

- Exibir indicadores gerenciais da oficina.

Regras de negocio:

- Apenas perfis administrativos podem consultar.
- Indicadores devem considerar tenant atual.
- Deve retornar total de produtos.
- Deve retornar produtos abaixo do minimo.
- Deve retornar produtos zerados.
- Deve retornar valor total em estoque.
- Deve retornar movimentacoes do dia.
- Deve retornar movimentacoes recentes.
- Dashboard deve consultar dados, nao concentrar regras centrais de Inventory.

## UC13 - Cadastrar Veiculo

Modulo:

- Workshop.

Endpoint:

```text
POST /api/v1/vehicles
```

Objetivo:

- Cadastrar veiculo atendido pela oficina.

Regras de negocio:

- Perfis `owner`, `manager`, `admin` e `mechanic` podem cadastrar.
- Veiculo deve pertencer ao tenant atual.
- Placa, marca, modelo, ano, proprietario e telefone sao obrigatorios.
- Placa deve ser normalizada.
- Placa deve ser unica dentro do tenant.
- Veiculo de outro tenant nao deve interferir na validacao.

## UC14 - Criar Ordem De Servico

Modulo:

- Workshop.

Endpoint:

```text
POST /api/v1/service-orders
```

Objetivo:

- Criar ordem de servico para um veiculo da oficina.

Regras de negocio:

- Perfis `owner`, `manager`, `admin` e `mechanic` podem criar.
- Veiculo deve existir no tenant atual.
- OS deve pertencer ao tenant atual.
- Cliente e descricao dos servicos sao obrigatorios.
- Status inicial deve ser `open`.
- Usuario autenticado deve ser registrado como criador.
- OS de outro tenant nao pode ser acessada.

## UC15 - Adicionar Pecas A Ordem De Servico

Modulo:

- Workshop.

Endpoint:

```text
POST /api/v1/service-orders/{serviceOrder}/parts
```

Objetivo:

- Vincular pecas utilizadas a uma OS aberta.

Regras de negocio:

- Perfis `owner`, `manager`, `admin` e `mechanic` podem adicionar pecas.
- OS deve existir no tenant atual.
- OS deve estar aberta.
- Produto deve existir no tenant atual.
- Quantidade deve ser maior que zero.
- Adicionar peca nao deve baixar estoque imediatamente.
- Baixa definitiva ocorre no UC16.
- Usuario autenticado deve ser registrado como quem adicionou a peca.

## UC16 - Finalizar Ordem De Servico Com Baixa Automatica

Modulo:

- Workshop/Inventory.

Endpoint:

```text
PATCH /api/v1/service-orders/{serviceOrder}/finish
```

Objetivo:

- Finalizar OS e baixar automaticamente as pecas utilizadas.

Regras de negocio:

- Perfis `owner`, `manager`, `admin` e `mechanic` podem finalizar.
- OS deve existir no tenant atual.
- OS deve estar aberta.
- OS deve possuir pelo menos uma peca.
- Cada peca deve gerar uma movimentacao de saida.
- Saida deve usar tipo `service_consumption`.
- Saldo deve ser validado antes da finalizacao.
- Fluxo deve ser transacional.
- Se uma baixa falhar, OS deve permanecer aberta.
- OS finalizada deve registrar `finished_at`.
- Deve manter vinculo formal entre OS, item da OS e movimentacao.

## UC17 - Consultar Historico De Movimentacoes

Modulo:

- Inventory.

Endpoint:

```text
GET /api/v1/inventory/movements
```

Objetivo:

- Consultar historico/auditoria das movimentacoes de estoque.

Regras de negocio:

- Apenas perfis administrativos podem consultar.
- Consulta deve considerar tenant atual.
- Pode filtrar por produto.
- Pode filtrar por direcao.
- Pode filtrar por tipo.
- Pode filtrar por usuario.
- Pode filtrar por periodo.
- Deve retornar produto, usuario, motivo, quantidade, tipo, data e vinculo com OS, quando existir.

## UC18 - Consultar Produtos Mais Consumidos

Modulo:

- Dashboard/Reporting.

Endpoint:

```text
GET /api/v1/dashboard/most-consumed-products
```

Objetivo:

- Listar produtos com maior consumo no periodo.

Regras de negocio:

- Apenas perfis administrativos podem consultar.
- Consulta deve considerar tenant atual.
- Deve considerar movimentacoes de saida.
- Pode filtrar por periodo.
- Deve retornar quantidade total consumida.
- Deve retornar quantidade de movimentacoes.
- Deve respeitar limite de retorno.
- Deve apoiar decisao de compra e reposicao.

## UC19 - Gerenciar Configuracoes Da Oficina

Modulo:

- Settings.

Endpoints:

```text
GET /api/v1/settings/workshop
PATCH /api/v1/settings/workshop
```

Objetivo:

- Consultar e atualizar configuracoes cadastrais, operacionais e notificacoes da oficina.

Regras de negocio:

- Apenas perfis administrativos podem acessar.
- Configuracoes pertencem ao tenant atual.
- Ao consultar e nao existir configuracao, o backend cria configuracoes padrao.
- Nome exibido e obrigatorio.
- Fuso horario e obrigatorio.
- Moeda suportada atualmente: `BRL`.
- Estoque minimo padrao nao pode ser negativo.
- Documento e telefone devem ser normalizados para digitos.
- E-mails devem ser normalizados para minusculo.
- Dados de plano podem ser consultados.
- Plano e limite de usuarios nao devem ser alterados diretamente por esse endpoint.
- Alteracoes operacionais devem valer para acoes futuras.

## Casos De Uso De Consulta

Alguns casos de uso usam queries em vez de repositories de dominio:

- UC12 - Dashboard.
- UC17 - Historico de movimentacoes.
- UC18 - Produtos mais consumidos.
- Listagem de veiculos.
- Listagem/detalhamento de ordens de servico.

Regra:

```text
Queries podem ser otimizadas para leitura, mas devem respeitar tenant e permissoes.
```

## Regras Transversais

## Multi-Tenant

- Todo dado operacional deve pertencer a um tenant.
- Nenhum caso de uso pode retornar dados de outro tenant.
- Repositories e queries devem filtrar por tenant.
- Tenant vem do token autenticado.

## Auditoria Operacional

- Movimentacoes de estoque devem registrar usuario, data, motivo, produto, tipo e quantidade.
- OS deve manter historico das pecas adicionadas.
- Finalizacao de OS deve manter vinculo com movimentacoes.

## Estoque

- Saldo nao e editado diretamente pelo cadastro de produto.
- Saldo muda por entrada, saida, ajuste ou finalizacao de OS.
- Saidas devem validar saldo disponivel.
- Ajustes manuais exigem justificativa.

## Permissoes

- Backoffice: `owner`, `manager`, `admin`.
- Operacao da oficina: `owner`, `manager`, `admin`, `mechanic`.
- Mecanico nao gerencia usuarios, configuracoes, produtos ou movimentacoes manuais.

## Padrao De Resposta

Respostas de recurso unico:

```json
{
  "data": {}
}
```

Respostas de listagem:

```json
{
  "data": [],
  "meta": {
    "total": 0
  }
}
```

Erros:

```json
{
  "message": "Descricao do erro."
}
```

Erros de validacao:

```json
{
  "message": "The given data was invalid.",
  "errors": {}
}
```


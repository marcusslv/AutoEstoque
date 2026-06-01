# AutoEstoque - Analise Arquitetural Com Clean Architecture E DDD

## 1. Objetivo

Este documento define uma proposta inicial de arquitetura para implementacao do AutoEstoque usando Clean Architecture, Domain-Driven Design e Modular Monolith.

O objetivo nao e criar complexidade desnecessaria, mas proteger o dominio principal do produto: controle de estoque para oficinas mecanicas.

## 2. Decisao Arquitetural Recomendada

Para o AutoEstoque, a recomendacao e usar:

- Backend em Laravel 13.
- Modular Monolith.
- Clean Architecture dentro de cada modulo.
- DDD tatico nos modulos com regra de negocio relevante.
- API First.
- Multi-Tenant desde o inicio.

Essa abordagem permite comecar simples, mantendo separacao suficiente para o produto crescer sem virar um bloco dificil de manter.

## 3. Por Que Modular Monolith

O AutoEstoque tem varios dominios relacionados, mas ainda nao justifica microservicos no MVP.

Um monolito modular e mais adequado porque:

- Reduz custo operacional.
- Evita distribuicao prematura.
- Facilita desenvolvimento rapido do MVP.
- Mantem transacoes locais no banco.
- Permite separar responsabilidades por modulo.
- Pode evoluir para servicos separados no futuro, se houver necessidade real.

## 4. Bounded Contexts Iniciais

### 4.1 Identity & Access

Responsavel por autenticacao, usuarios, permissoes e vinculo com oficinas.

Casos de uso relacionados:

- UC01 - Autenticar usuario.
- UC02 - Recuperar senha.
- UC03 - Gerenciar usuarios da oficina.

### 4.2 Tenant/Organization

Responsavel pela separacao multiempresa.

Conceitos:

- Oficina.
- Plano.
- Assinatura.
- Limites do plano.

No MVP, pode ficar junto de Identity ou em modulo proprio simples. A recomendacao e separar conceitualmente desde o inicio, mesmo que a implementacao seja pequena.

### 4.3 Catalog

Responsavel pelo cadastro de produtos, categorias, marcas e fornecedores.

Casos de uso relacionados:

- UC04 - Cadastrar produto/peca.
- UC05 - Editar produto/peca.
- UC06 - Consultar estoque, na parte de consulta cadastral.

### 4.4 Inventory

Responsavel por saldo, movimentacoes e alertas de estoque.

Casos de uso relacionados:

- UC06 - Consultar estoque.
- UC07 - Registrar entrada de estoque.
- UC08 - Registrar saida de estoque.
- UC09 - Registrar ajuste manual.
- UC10 - Gerar alerta de estoque minimo.
- UC11 - Gerar alerta de estoque zerado.
- UC17 - Consultar historico de movimentacoes.
- UC18 - Consultar produtos mais consumidos.

### 4.5 Workshop Operations

Responsavel por veiculos e ordens de servico.

Casos de uso relacionados:

- UC13 - Cadastrar veiculo.
- UC14 - Criar ordem de servico.
- UC15 - Adicionar pecas a ordem de servico.
- UC16 - Finalizar ordem de servico com baixa automatica.

### 4.6 Dashboard/Reporting

Responsavel por indicadores, consultas agregadas e relatorios.

Casos de uso relacionados:

- UC12 - Visualizar dashboard.
- UC18 - Consultar produtos mais consumidos.

Este contexto deve consumir dados dos outros modulos por consultas otimizadas, sem virar dono das regras de estoque.

## 5. Camadas Da Clean Architecture

Cada modulo de negocio deve seguir a separacao abaixo.

### 5.1 Domain

Camada mais importante. Contem regras de negocio puras.

Responsabilidades:

- Entidades.
- Value Objects.
- Domain Services.
- Domain Events.
- Excecoes de dominio.
- Interfaces de repositorio, quando fizer sentido para expressar necessidade do dominio.

Nao deve depender de Laravel, Eloquent, HTTP, Request, Response, Queue ou banco.

### 5.2 Application

Camada de casos de uso.

Responsabilidades:

- Orquestrar fluxos.
- Abrir transacoes, se a convencao do projeto permitir.
- Chamar repositorios.
- Validar regras de aplicacao.
- Publicar eventos.
- Receber DTOs de entrada dos casos de uso.
- Retornar DTOs de saida dos casos de uso.

Nao deve conhecer detalhes de controller, Eloquent ou request HTTP.

### 5.3 Infrastructure

Camada que conversa com ferramentas externas.

Responsabilidades:

- Repositorios Eloquent.
- Models Eloquent.
- Migrations.
- Integracoes externas.
- Implementacoes de filas.
- Implementacoes de notificacao.
- Persistencia.

Pode depender de Laravel.

### 5.4 Interfaces

Camada de entrada e saida da aplicacao.

Responsabilidades:

- Controllers.
- Form Requests.
- API Resources.
- Rotas.
- Presenters.

Converte HTTP em DTOs de Input da camada Application e formata DTOs de Output em respostas externas.

## 6. DTOs De Input E Output Nos Use Cases

Cada caso de uso deve ter uma entrada e uma saida explicitas.

A recomendacao para o AutoEstoque e usar DTOs imutaveis para representar esses dados:

- `Input`: dados necessarios para executar o caso de uso.
- `Output`: dados retornados pelo caso de uso.

Essa convencao evita que a camada Application dependa de `Request`, `Model`, arrays soltos ou estruturas especificas do framework.

## 6.1 Conceito De Input

O `Input` representa a intencao de executar um caso de uso.

Ele deve conter apenas os dados que o caso de uso precisa para funcionar.

Exemplo:

```php
final readonly class CreateProductInput
{
    public function __construct(
        public string $tenantId,
        public string $userId,
        public string $name,
        public string $sku,
        public ?string $barcode,
        public ?string $category,
        public ?string $brand,
        public ?string $supplier,
        public int $minimumStock,
        public int $costInCents,
    ) {}
}
```

O `Input` pode receber dados primitivos, como `string` e `int`, e o use case converte esses dados para Value Objects do dominio.

Exemplo:

```php
$tenantId = new TenantId($input->tenantId);
$sku = new Sku($input->sku);
```

Essa escolha mantem a fronteira da aplicacao simples e impede que controllers precisem conhecer detalhes internos do dominio.

## 6.2 Conceito De Output

O `Output` representa o resultado do caso de uso.

Ele deve conter apenas os dados que a camada externa precisa para montar uma resposta, tela ou notificacao.

Exemplo:

```php
final readonly class CreateProductOutput
{
    public function __construct(
        public string $id,
        public string $name,
        public string $sku,
        public int $minimumStock,
        public int $costInCents,
    ) {}
}
```

O `Output` nao deve ser um Model Eloquent e nao deve expor a entidade de dominio diretamente.

Isso permite que:

- A API mude sua representacao sem alterar o dominio.
- O dominio evolua sem quebrar controllers.
- Testes de use case sejam simples.
- A camada Application continue independente do framework.

## 6.3 Diferenca Entre Input, Command E Request

No Laravel, e comum existir um `FormRequest` para validar entrada HTTP.

Na arquitetura proposta, os papeis ficam separados:

| Elemento | Camada | Responsabilidade |
| --- | --- | --- |
| `CreateProductRequest` | Interfaces | Validar formato da requisicao HTTP |
| `CreateProductInput` | Application | Transportar dados para o caso de uso |
| `CreateProductUseCase` | Application | Executar a regra de aplicacao |
| `CreateProductOutput` | Application | Transportar resultado do caso de uso |
| `CreateProductPresenter` | Interfaces | Formatar o Output em resposta externa |
| `ProductResource` | Interfaces | Opcional para consultas e listagens simples |

O controller deve converter `Request` em `Input`:

```php
$output = $useCase->execute(new CreateProductInput(
    tenantId: $request->user()->tenant_id,
    userId: $request->user()->id,
    name: $request->string('name')->toString(),
    sku: $request->string('sku')->toString(),
    barcode: $request->input('barcode'),
    category: $request->input('category'),
    brand: $request->input('brand'),
    supplier: $request->input('supplier'),
    minimumStock: $request->integer('minimum_stock'),
    costInCents: $request->integer('cost_in_cents'),
));
```

O controller deve converter `Output` em resposta HTTP:

```php
return response()->json([
    'data' => [
        'id' => $output->id,
        'name' => $output->name,
        'sku' => $output->sku,
    ],
], 201);
```

## 6.4 Convencao De Nomes

Para padronizar o projeto, recomenda-se:

```text
CreateProduct/
  CreateProductInput.php
  CreateProductOutput.php
  CreateProductUseCase.php
```

Evite misturar nomes sem necessidade, como `Command`, `Payload`, `Data`, `DTO` e `Input` no mesmo projeto.

Para o AutoEstoque, a convencao recomendada e:

- `Input` para entrada de use case.
- `Output` para saida de use case.

O termo `DTO` pode aparecer no texto tecnico, mas os arquivos devem usar nomes explicitos como `CreateProductInput` e `CreateProductOutput`.

## 6.5 Quando Um Use Case Nao Precisa Retornar Dados

Mesmo quando o caso de uso nao precisa retornar muitos dados, ainda e valido ter um `Output` simples.

Exemplo:

```php
final readonly class RegisterStockOutput
{
    public function __construct(
        public string $movementId,
        public int $currentStock,
    ) {}
}
```

Para operacoes realmente sem retorno, pode-se usar `void`, mas a recomendacao e retornar pelo menos um identificador ou estado relevante quando isso ajudar a API e os testes.

## 6.6 O Que Nao Deve Entrar Em Input E Output

Evite colocar em `Input`:

- Objetos `Request`.
- Models Eloquent.
- Dados que devem ser obtidos pelo contexto autenticado, como `tenantId`, vindos diretamente do payload.
- Estruturas misturadas de multiplas responsabilidades.

Evite colocar em `Output`:

- Models Eloquent.
- Entidades de dominio completas.
- Objetos de resposta HTTP.
- Dados internos que nao precisam sair da aplicacao.

## 7. Presenters

Presenters sao componentes da camada `Interfaces` responsaveis por transformar um `Output` da camada Application em uma representacao externa.

No AutoEstoque, eles devem ser usados para manter controllers simples e evitar que regras de formatacao de resposta fiquem espalhadas.

## 7.1 Papel Do Presenter

O Presenter recebe um DTO de `Output` e devolve uma estrutura pronta para a interface de saida.

Exemplo:

```php
final class CreateProductPresenter
{
    public function present(CreateProductOutput $output): array
    {
        return [
            'data' => [
                'id' => $output->id,
                'name' => $output->name,
                'sku' => $output->sku,
                'minimum_stock' => $output->minimumStock,
                'cost_in_cents' => $output->costInCents,
            ],
        ];
    }
}
```

Com isso, o controller fica responsavel apenas por:

- Receber a requisicao.
- Montar o `Input`.
- Chamar o use case.
- Delegar a formatacao ao Presenter.
- Retornar a resposta HTTP.

## 7.2 Presenter Nao E Use Case

Presenter nao deve executar regra de negocio.

Ele tambem nao deve:

- Consultar banco de dados.
- Chamar repositorios.
- Alterar entidades.
- Decidir se uma operacao pode ou nao acontecer.
- Conhecer regras internas do dominio.

Seu papel e adaptar dados para a saida.

## 7.3 Presenter Versus API Resource

No Laravel, `JsonResource` tambem e usado para formatar respostas.

As duas opcoes sao validas, mas tem usos diferentes:

| Elemento | Melhor uso |
| --- | --- |
| Presenter | Formatar `Output` de use case |
| API Resource | Formatar models ou estruturas simples de leitura |

Como o AutoEstoque usa Clean Architecture, a recomendacao principal e:

- Para comandos e casos de uso transacionais, usar `Presenter`.
- Para endpoints simples de listagem/consulta, pode usar `Resource`, desde que ele nao receba Model Eloquent diretamente fora da camada adequada.

Exemplos:

- `POST /api/products`: usar `CreateProductPresenter`.
- `GET /api/products`: pode usar `ProductListPresenter` ou `ProductResource`, dependendo da estrategia de leitura.
- `GET /api/dashboard`: usar `DashboardPresenter`, porque a resposta e agregada e nao representa uma entidade unica.

## 7.4 Fluxo Com Presenter

Fluxo recomendado:

```text
Controller
  -> monta Input
  -> chama UseCase
  -> recebe Output
  -> chama Presenter
  -> retorna JsonResponse
```

Exemplo:

```php
public function __invoke(CreateProductRequest $request): JsonResponse
{
    $output = $this->useCase->execute($input);

    return response()->json(
        $this->presenter->present($output),
        201,
    );
}
```

## 7.5 Onde Colocar Presenters

Recomendacao de estrutura:

```text
app/
  Modules/
    Catalog/
      Interfaces/
        Http/
          Presenters/
            CreateProductPresenter.php
```

Presenters ficam na camada `Interfaces` porque representam a forma como a aplicacao se comunica com o mundo externo.

## 7.6 Presenters Para Web, Mobile E API

Como o AutoEstoque tem web e mobile, Presenters ajudam a manter consistencia das respostas.

No inicio, uma unica resposta JSON pode atender web e mobile.

Se no futuro houver necessidades diferentes, o mesmo `Output` pode ser apresentado de formas diferentes:

```text
CreateProductOutput
  -> CreateProductApiPresenter
  -> CreateProductMobilePresenter
  -> CreateProductAdminPresenter
```

Essa separacao evita mudar o use case apenas porque uma interface precisa exibir dados de outro jeito.

## 7.7 Convencao Recomendada

Para o AutoEstoque:

- Todo use case de escrita deve ter `Input`, `Output` e, quando exposto por HTTP, um `Presenter`.
- Controllers nao devem montar arrays grandes de resposta.
- Presenters podem retornar arrays simples.
- API Resources podem ser usados em consultas simples, com cuidado para nao vazar Eloquent para Application ou Domain.

Exemplo de pasta:

```text
CreateProduct/
  CreateProductInput.php
  CreateProductOutput.php
  CreateProductUseCase.php

Interfaces/
  Http/
    Controllers/
      CreateProductController.php
    Presenters/
      CreateProductPresenter.php
    Requests/
      CreateProductRequest.php
```

## 8. Regra De Dependencia

A dependencia deve sempre apontar para dentro:

```text
Interfaces -> Application -> Domain
Infrastructure -> Application/Domain
```

O Domain nao depende de ninguem.

O Application depende do Domain e de contratos.

A Infrastructure implementa contratos definidos pelo Domain ou Application.

## 9. Estrutura De Pastas Recomendada

```text
app/
  Modules/
    Catalog/
      Domain/
        Entities/
        ValueObjects/
        Repositories/
        Exceptions/
        Events/
      Application/
        UseCases/
      Infrastructure/
        Persistence/
          Eloquent/
            Models/
            Repositories/
        Database/
          Migrations/
      Interfaces/
        Http/
          Controllers/
          Requests/
          Presenters/
          Resources/
        Routes/

    Inventory/
      Domain/
      Application/
      Infrastructure/
      Interfaces/

    Workshop/
      Domain/
      Application/
      Infrastructure/
      Interfaces/

    Identity/
      Domain/
      Application/
      Infrastructure/
      Interfaces/

    Dashboard/
      Application/
      Infrastructure/
      Interfaces/
```

## 10. Alternativa Mais Simples Para O MVP

Se a estrutura acima parecer pesada para o primeiro commit, uma opcao intermediaria e:

```text
app/
  Domain/
    Catalog/
    Inventory/
    Workshop/
    Identity/
  Application/
    Catalog/
    Inventory/
    Workshop/
    Identity/
  Infrastructure/
    Persistence/
  Http/
    Controllers/
    Requests/
    Resources/
```

Mesmo assim, para um SaaS modular, a estrutura por modulo tende a ser mais clara.

## 11. Modelagem Inicial Do Dominio

### Catalog

Entidades:

- Product.
- Category.
- Brand.
- Supplier.

Value Objects:

- ProductId.
- TenantId.
- SKU.
- Barcode.
- Money.

Regras:

- SKU deve ser unico por oficina.
- Produto pertence a uma oficina.
- Custo nao pode ser negativo.

### Inventory

Entidades:

- StockItem ou InventoryItem.
- StockMovement.
- StockAlert.

Value Objects:

- Quantity.
- MovementType.
- MovementReason.

Regras:

- Entrada aumenta saldo.
- Saida reduz saldo.
- Saida nao deve gerar saldo negativo, salvo regra autorizada.
- Toda movimentacao exige usuario, data, produto, tipo, quantidade e motivo.
- Estoque abaixo do minimo gera alerta.
- Estoque zerado gera alerta especifico.

### Workshop

Entidades:

- Vehicle.
- ServiceOrder.
- ServiceOrderItem.

Regras:

- OS aberta pode receber pecas.
- OS finalizada nao deve receber alteracoes comuns.
- Finalizar OS deve baixar estoque das pecas utilizadas.
- Baixa automatica deve ser atomica.

## 12. Agregados Recomendados

### Product

Agregado do Catalog.

Raiz:

- Product.

Responsavel por:

- Dados cadastrais da peca.
- SKU.
- Codigo de barras.
- Estoque minimo como configuracao.
- Custo de aquisicao.

Nao deve ser responsavel por:

- Historico de movimentacoes.
- Calculo de consumo.
- Baixa de estoque.

### InventoryItem

Agregado do Inventory.

Raiz:

- InventoryItem.

Responsavel por:

- Saldo atual.
- Entrada.
- Saida.
- Validacao de saldo.
- Emissao de eventos de estoque baixo ou zerado.

### ServiceOrder

Agregado do Workshop.

Raiz:

- ServiceOrder.

Responsavel por:

- Servicos realizados.
- Pecas utilizadas.
- Status da OS.
- Finalizacao da OS.

Nao deve alterar estoque diretamente. Deve chamar um caso de uso do Inventory ou publicar evento de dominio/aplicacao para baixa.

## 13. Multi-Tenant

O AutoEstoque deve nascer multi-tenant.

Recomendacao inicial:

- Usar tenant_id em todas as tabelas de negocio.
- Aplicar escopo por tenant nos repositorios.
- Nunca receber tenant_id livremente do payload da API.
- Obter tenant atual pelo usuario autenticado ou contexto selecionado.
- Criar um TenantContext para representar a oficina atual.

Exemplo conceitual:

```php
final readonly class TenantContext
{
    public function __construct(
        public string $tenantId,
        public string $userId,
    ) {}
}
```

## 14. Eventos

Eventos ajudam a desacoplar modulos.

Eventos iniciais recomendados:

- ProductCreated.
- StockEntered.
- StockRemoved.
- StockReachedMinimum.
- StockReachedZero.
- ServiceOrderFinished.

Uso sugerido:

- Inventory gera eventos quando saldo muda.
- Alertas reagem a eventos de estoque.
- Dashboard pode se beneficiar de projections futuramente.

No MVP, eventos podem ser sincronos dentro da aplicacao. Filas podem entrar quando houver necessidade.

## 15. Transacoes

Operacoes criticas devem ser atomicas:

- Registrar entrada de estoque.
- Registrar saida de estoque.
- Registrar ajuste manual.
- Finalizar ordem de servico com baixa automatica.

No Laravel, a transacao pode ficar no Application Service/Use Case ou em um TransactionManager abstrato.

Para manter Clean Architecture, prefira contrato:

```php
interface TransactionManager
{
    public function run(callable $callback): mixed;
}
```

A Infrastructure implementa usando `DB::transaction`.

## 16. Repositorios

Repositorios devem representar necessidades do caso de uso, nao simplesmente CRUD generico.

Exemplo:

```php
interface ProductRepository
{
    public function existsBySku(TenantId $tenantId, Sku $sku): bool;

    public function save(Product $product): void;
}
```

Evite criar repositorios genericos do tipo `BaseRepository` no inicio.

## 17. Validacoes

Existem tres niveis de validacao:

### HTTP/Form Request

Valida formato da entrada:

- Campo obrigatorio.
- String.
- Numero.
- Tamanho maximo.

### Application

Valida regras do fluxo:

- Usuario tem permissao.
- Plano permite criar usuario.
- Produto pode ser movimentado.

### Domain

Valida invariantes:

- Custo nao pode ser negativo.
- Quantidade deve ser positiva.
- Saida nao pode exceder saldo.
- SKU nao pode ser vazio.

## 18. Como Lidar Com Eloquent

Eloquent deve ficar na Infrastructure.

O dominio nao deve estender Model e nao deve usar casts do Laravel como regra central.

Fluxo recomendado:

```text
Controller
  -> Use Case
    -> Repository Interface
      -> Eloquent Repository
        -> Eloquent Model
```

O Eloquent Repository converte Model em Entidade de Dominio e Entidade em Model.

## 19. API First

Cada caso de uso deve ser exposto por endpoints claros.

Exemplos:

```text
POST   /api/products
GET    /api/products
PATCH  /api/products/{id}

POST   /api/inventory/entries
POST   /api/inventory/outputs
POST   /api/inventory/adjustments
GET    /api/inventory/movements

GET    /api/dashboard
```

## 20. Riscos Da Arquitetura

### Risco 1 - Excesso De Camadas Para O MVP

Mitigacao:

- Aplicar DDD completo apenas nos modulos com regra relevante.
- Usar CRUD mais simples onde nao houver complexidade.

### Risco 2 - Duplicar Entidade E Model Sem Necessidade

Mitigacao:

- Separar entidade de dominio apenas nos agregados importantes.
- Catalog e Inventory justificam essa separacao.

### Risco 3 - Repositorios Virarem CRUD Generico

Mitigacao:

- Criar metodos orientados a casos de uso.
- Evitar abstrair tudo antes da necessidade.

### Risco 4 - Multi-Tenant Fragil

Mitigacao:

- TenantContext obrigatorio.
- Testes garantindo isolamento entre oficinas.
- Repositorios sempre filtrando por tenant.

## 21. Decisoes Recomendadas Para O MVP

- Comecar com modulos Catalog, Inventory e Identity.
- Implementar Product como entidade de dominio.
- Implementar InventoryItem e StockMovement como dominio forte.
- Usar Eloquent apenas em Infrastructure.
- Criar TenantContext desde o primeiro caso de uso.
- Criar testes de dominio e aplicacao antes dos testes HTTP mais amplos.

## 22. Ordem Sugerida De Implementacao

1. Estrutura modular do backend Laravel.
2. TenantContext.
3. Autenticacao basica.
4. Catalog/Product.
5. Inventory/Movements.
6. Alertas de estoque.
7. Dashboard basico.
8. Workshop/ServiceOrder.

# Exemplo De Implementacao - UC04 Cadastrar Produto/Peca

Este documento apresenta um exemplo de implementacao do caso de uso UC04 usando Clean Architecture e DDD em um backend Laravel.

O exemplo e propositalmente parcial. Ele serve para analise arquitetural antes da implementacao real.

## 1. Caso De Uso

UC04 - Cadastrar produto/peca.

Objetivo:

Permitir que a oficina registre uma peca no estoque.

Fluxo resumido:

1. Controller recebe dados HTTP.
2. Request valida formato dos dados.
3. Controller cria DTO de Input da aplicacao.
4. Use Case valida regra de SKU unico por tenant.
5. Entidade Product e criada.
6. Repository persiste a entidade usando Eloquent.
7. Use Case retorna DTO de Output.

## 2. Estrutura De Arquivos Do Exemplo

```text
app/
  Modules/
    Catalog/
      Domain/
        Entities/
          Product.php
        Factories/
          ProductFactory.php
        ValueObjects/
          ProductId.php
          TenantId.php
          Sku.php
          Barcode.php
          Money.php
        Validators/
          ProductValidator.php
        Repositories/
          ProductRepository.php
        Exceptions/
          DuplicatedSkuException.php
      Application/
        UseCases/
          CreateProduct/
            CreateProductInput.php
            CreateProductOutput.php
            CreateProductUseCase.php
      Infrastructure/
        Persistence/
          Eloquent/
            Models/
              ProductModel.php
            Repositories/
              EloquentProductRepository.php
      Interfaces/
        Http/
          Controllers/
            CreateProductController.php
          Presenters/
            CreateProductPresenter.php
          Requests/
            CreateProductRequest.php
          Resources/
            ProductResource.php
```

## 3. Domain - Value Objects

### TenantId

```php
<?php

namespace App\Modules\Catalog\Domain\ValueObjects;

final readonly class TenantId
{
    public function __construct(public string $value)
    {
        if ($this->value === '') {
            throw new \InvalidArgumentException('TenantId nao pode ser vazio.');
        }
    }
}
```

### ProductId

```php
<?php

namespace App\Modules\Catalog\Domain\ValueObjects;

final readonly class ProductId
{
    public function __construct(public string $value)
    {
        if ($this->value === '') {
            throw new \InvalidArgumentException('ProductId nao pode ser vazio.');
        }
    }
}
```

### Sku

```php
<?php

namespace App\Modules\Catalog\Domain\ValueObjects;

final readonly class Sku
{
    public function __construct(public string $value)
    {
        $value = trim($this->value);

        if ($value === '') {
            throw new \InvalidArgumentException('SKU nao pode ser vazio.');
        }
    }
}
```

### Barcode

```php
<?php

namespace App\Modules\Catalog\Domain\ValueObjects;

final readonly class Barcode
{
    public function __construct(public ?string $value)
    {
        if ($this->value !== null && trim($this->value) === '') {
            throw new \InvalidArgumentException('Codigo de barras invalido.');
        }
    }
}
```

### Money

```php
<?php

namespace App\Modules\Catalog\Domain\ValueObjects;

final readonly class Money
{
    public function __construct(
        public int $amountInCents,
        public string $currency = 'BRL',
    ) {
        if ($this->amountInCents < 0) {
            throw new \InvalidArgumentException('Valor monetario nao pode ser negativo.');
        }
    }
}
```

## 4. Domain - Entidade Product

Entidades devem aplicar validators de dominio para proteger suas invariantes.

Value Objects podem manter validacoes simples internamente, mas a entidade deve delegar suas validacoes para um validator puro, por exemplo `ProductValidator`.

A criacao de entidades deve ser feita por factories de dominio. Por isso, o use case nao deve chamar `new Product()` diretamente.

```php
<?php

namespace App\Modules\Catalog\Domain\Entities;

use App\Modules\Catalog\Domain\ValueObjects\Barcode;
use App\Modules\Catalog\Domain\ValueObjects\Money;
use App\Modules\Catalog\Domain\ValueObjects\ProductId;
use App\Modules\Catalog\Domain\ValueObjects\Sku;
use App\Modules\Catalog\Domain\ValueObjects\TenantId;
use App\Modules\Catalog\Domain\Validators\ProductValidator;
use App\Modules\Shared\Domain\Entities\Entity;

final class Product extends Entity
{
    public function __construct(
        private readonly ProductId $id,
        private readonly TenantId $tenantId,
        private readonly string $name,
        private readonly Sku $sku,
        private readonly Barcode $barcode,
        private readonly ?string $category,
        private readonly ?string $brand,
        private readonly ?string $supplier,
        private readonly int $minimumStock,
        private readonly Money $cost,
    ) {
        parent::__construct();

        ProductValidator::validate($this);

        $this->throwIfNotificationHasErrors();
    }

    public function id(): ProductId
    {
        return $this->id;
    }

    public function tenantId(): TenantId
    {
        return $this->tenantId;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function sku(): Sku
    {
        return $this->sku;
    }

    public function barcode(): Barcode
    {
        return $this->barcode;
    }

    public function category(): ?string
    {
        return $this->category;
    }

    public function brand(): ?string
    {
        return $this->brand;
    }

    public function supplier(): ?string
    {
        return $this->supplier;
    }

    public function minimumStock(): int
    {
        return $this->minimumStock;
    }

    public function cost(): Money
    {
        return $this->cost;
    }
}
```

## 4.1 Domain - Factory

Factories de dominio centralizam a criacao de entidades novas.

Elas podem normalizar valores antes da entidade ser instanciada, como aplicar `trim` em textos opcionais.

Exemplo:

```php
<?php

namespace App\Modules\Catalog\Domain\Factories;

use App\Modules\Catalog\Domain\Entities\Product;
use App\Modules\Catalog\Domain\ValueObjects\Barcode;
use App\Modules\Catalog\Domain\ValueObjects\Money;
use App\Modules\Catalog\Domain\ValueObjects\ProductId;
use App\Modules\Catalog\Domain\ValueObjects\Sku;
use App\Modules\Tenant\Domain\ValueObjects\TenantId;

final class ProductFactory
{
    public function create(
        ProductId $id,
        TenantId $tenantId,
        string $name,
        Sku $sku,
        Barcode $barcode,
        ?string $category,
        ?string $brand,
        ?string $supplier,
        int $minimumStock,
        Money $cost,
    ): Product {
        return new Product(
            id: $id,
            tenantId: $tenantId,
            name: trim($name),
            sku: $sku,
            barcode: $barcode,
            category: $this->nullableTrim($category),
            brand: $this->nullableTrim($brand),
            supplier: $this->nullableTrim($supplier),
            minimumStock: $minimumStock,
            cost: $cost,
        );
    }

    private function nullableTrim(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }
}
```

O use case deve usar a factory:

```php
$product = $this->productFactory->create(
    id: $productId,
    tenantId: $tenantId,
    name: $input->name,
    sku: $sku,
    barcode: $barcode,
    category: $input->category,
    brand: $input->brand,
    supplier: $input->supplier,
    minimumStock: $input->minimumStock,
    cost: $cost,
);
```

## 4.2 Domain - Validator

Validators de dominio devem ser usados com Notification Pattern para organizar invariantes de entidades e aggregates.

Com esse padrao, o validator nao lanca excecao na primeira falha. Ele acumula erros na `Notification` da propria entidade, permitindo que a entidade bloqueie o estado invalido com todos os erros encontrados.

Eles nao devem usar:

- `FormRequest`.
- `Illuminate\Validation\Validator`.
- Eloquent.
- Banco de dados.
- HTTP.

Exemplo:

```php
<?php

namespace App\Modules\Catalog\Domain\Validators;

use App\Modules\Catalog\Domain\Entities\Product;

final class ProductValidator
{
    public static function validate(Product $product): void
    {
        if (trim($product->name()) === '') {
            $product->notification()->add(
                field: 'name',
                message: 'Product name is required.',
                code: 'product.name_required',
            );
        }

        if ($product->minimumStock() < 0) {
            $product->notification()->add(
                field: 'minimum_stock',
                message: 'Minimum stock cannot be negative.',
                code: 'product.minimum_stock_negative',
            );
        }
    }
}
```

Uso dentro da entidade:

```php
final class Product extends Entity
{
    public function __construct(...)
    {
        parent::__construct();

        ProductValidator::validate($this);

        $this->throwIfNotificationHasErrors();
    }
}
```

Para o UC04, `ProductValidator` deve ser aplicado pela entidade `Product`. Validacoes simples de valores individuais continuam dentro dos Value Objects, como `Sku`, `Barcode`, `Money` e `ProductId`.

Estrutura compartilhada recomendada:

```text
app/
  Modules/
    Shared/
      Domain/
        Entities/
          Entity.php
        Notifications/
          Notification.php
          NotificationError.php
        Exceptions/
          DomainValidationException.php
```

## 5. Domain - Contrato Do Repositorio

```php
<?php

namespace App\Modules\Catalog\Domain\Repositories;

use App\Modules\Catalog\Domain\Entities\Product;
use App\Modules\Catalog\Domain\ValueObjects\Sku;
use App\Modules\Catalog\Domain\ValueObjects\TenantId;

interface ProductRepository
{
    public function existsBySku(TenantId $tenantId, Sku $sku): bool;

    public function save(Product $product): void;
}
```

## 6. Domain - Excecao De SKU Duplicado

```php
<?php

namespace App\Modules\Catalog\Domain\Exceptions;

use DomainException;

final class DuplicatedSkuException extends DomainException
{
    public static function forSku(string $sku): self
    {
        return new self("Ja existe um produto cadastrado com o SKU {$sku}.");
    }
}
```

## 7. Application - Input

```php
<?php

namespace App\Modules\Catalog\Application\UseCases\CreateProduct;

final readonly class CreateProductInput
{
    public function __construct(
        public string $tenantId,
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

## 8. Application - Output

```php
<?php

namespace App\Modules\Catalog\Application\UseCases\CreateProduct;

final readonly class CreateProductOutput
{
    public function __construct(
        public string $id,
        public string $name,
        public string $sku,
    ) {}
}
```

## 9. Application - Use Case

```php
<?php

namespace App\Modules\Catalog\Application\UseCases\CreateProduct;

use App\Modules\Catalog\Domain\Entities\Product;
use App\Modules\Catalog\Domain\Exceptions\DuplicatedSkuException;
use App\Modules\Catalog\Domain\Repositories\ProductRepository;
use App\Modules\Catalog\Domain\ValueObjects\Barcode;
use App\Modules\Catalog\Domain\ValueObjects\Money;
use App\Modules\Catalog\Domain\ValueObjects\ProductId;
use App\Modules\Catalog\Domain\ValueObjects\Sku;
use App\Modules\Catalog\Domain\ValueObjects\TenantId;
use Illuminate\Support\Str;

final readonly class CreateProductUseCase
{
    public function __construct(
        private ProductRepository $products,
    ) {}

    public function execute(CreateProductInput $input): CreateProductOutput
    {
        $tenantId = new TenantId($input->tenantId);
        $sku = new Sku($input->sku);

        if ($this->products->existsBySku($tenantId, $sku)) {
            throw DuplicatedSkuException::forSku($sku->value);
        }

        $product = Product::create(
            id: new ProductId((string) Str::uuid()),
            tenantId: $tenantId,
            name: $input->name,
            sku: $sku,
            barcode: new Barcode($input->barcode),
            category: $input->category,
            brand: $input->brand,
            supplier: $input->supplier,
            minimumStock: $input->minimumStock,
            cost: new Money($input->costInCents),
        );

        $this->products->save($product);

        return new CreateProductOutput(
            id: $product->id()->value,
            name: $product->name(),
            sku: $product->sku()->value,
        );
    }
}
```

## 10. Infrastructure - Eloquent Model

```php
<?php

namespace App\Modules\Catalog\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

final class ProductModel extends Model
{
    protected $table = 'products';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'tenant_id',
        'name',
        'sku',
        'barcode',
        'category',
        'brand',
        'supplier',
        'minimum_stock',
        'cost_in_cents',
        'currency',
    ];
}
```

## 11. Infrastructure - Eloquent Repository

```php
<?php

namespace App\Modules\Catalog\Infrastructure\Persistence\Eloquent\Repositories;

use App\Modules\Catalog\Domain\Entities\Product;
use App\Modules\Catalog\Domain\Repositories\ProductRepository;
use App\Modules\Catalog\Domain\ValueObjects\Sku;
use App\Modules\Catalog\Domain\ValueObjects\TenantId;
use App\Modules\Catalog\Infrastructure\Persistence\Eloquent\Models\ProductModel;

final class EloquentProductRepository implements ProductRepository
{
    public function existsBySku(TenantId $tenantId, Sku $sku): bool
    {
        return ProductModel::query()
            ->where('tenant_id', $tenantId->value)
            ->where('sku', $sku->value)
            ->exists();
    }

    public function save(Product $product): void
    {
        ProductModel::query()->create([
            'id' => $product->id()->value,
            'tenant_id' => $product->tenantId()->value,
            'name' => $product->name(),
            'sku' => $product->sku()->value,
            'barcode' => $product->barcode()->value,
            'category' => $product->category(),
            'brand' => $product->brand(),
            'supplier' => $product->supplier(),
            'minimum_stock' => $product->minimumStock(),
            'cost_in_cents' => $product->cost()->amountInCents,
            'currency' => $product->cost()->currency,
        ]);
    }
}
```

## 12. Interfaces - Form Request

```php
<?php

namespace App\Modules\Catalog\Interfaces\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CreateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:80'],
            'barcode' => ['nullable', 'string', 'max:120'],
            'category' => ['nullable', 'string', 'max:120'],
            'brand' => ['nullable', 'string', 'max:120'],
            'supplier' => ['nullable', 'string', 'max:120'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'cost_in_cents' => ['required', 'integer', 'min:0'],
        ];
    }
}
```

## 13. Interfaces - Presenter

```php
<?php

namespace App\Modules\Catalog\Interfaces\Http\Presenters;

use App\Modules\Catalog\Application\UseCases\CreateProduct\CreateProductOutput;

final class CreateProductPresenter
{
    public function present(CreateProductOutput $output): array
    {
        return [
            'data' => [
                'id' => $output->id,
                'name' => $output->name,
                'sku' => $output->sku,
            ],
        ];
    }
}
```

## 14. Interfaces - Controller

```php
<?php

namespace App\Modules\Catalog\Interfaces\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Catalog\Application\UseCases\CreateProduct\CreateProductInput;
use App\Modules\Catalog\Application\UseCases\CreateProduct\CreateProductUseCase;
use App\Modules\Catalog\Interfaces\Http\Presenters\CreateProductPresenter;
use App\Modules\Catalog\Interfaces\Http\Requests\CreateProductRequest;
use Illuminate\Http\JsonResponse;

final class CreateProductController extends Controller
{
    public function __construct(
        private readonly CreateProductUseCase $useCase,
        private readonly CreateProductPresenter $presenter,
    ) {}

    public function __invoke(CreateProductRequest $request): JsonResponse
    {
        $user = $request->user();

        $output = $this->useCase->execute(new CreateProductInput(
            tenantId: $user->tenant_id,
            name: $request->string('name')->toString(),
            sku: $request->string('sku')->toString(),
            barcode: $request->input('barcode'),
            category: $request->input('category'),
            brand: $request->input('brand'),
            supplier: $request->input('supplier'),
            minimumStock: $request->integer('minimum_stock'),
            costInCents: $request->integer('cost_in_cents'),
        ));

        return response()->json(
            $this->presenter->present($output),
            201,
        );
    }
}
```

## 15. Service Provider - Binding

```php
<?php

namespace App\Modules\Catalog\Infrastructure\Providers;

use App\Modules\Catalog\Domain\Repositories\ProductRepository;
use App\Modules\Catalog\Infrastructure\Persistence\Eloquent\Repositories\EloquentProductRepository;
use Illuminate\Support\ServiceProvider;

final class CatalogServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ProductRepository::class, EloquentProductRepository::class);
    }
}
```

## 16. Migration Conceitual

```php
Schema::create('products', function (Blueprint $table): void {
    $table->uuid('id')->primary();
    $table->uuid('tenant_id')->index();
    $table->string('name');
    $table->string('sku', 80);
    $table->string('barcode', 120)->nullable();
    $table->string('category', 120)->nullable();
    $table->string('brand', 120)->nullable();
    $table->string('supplier', 120)->nullable();
    $table->unsignedInteger('minimum_stock')->default(0);
    $table->unsignedBigInteger('cost_in_cents')->default(0);
    $table->string('currency', 3)->default('BRL');
    $table->timestamps();

    $table->unique(['tenant_id', 'sku']);
    $table->index(['tenant_id', 'barcode']);
});
```

## 17. Teste De Application

```php
<?php

use App\Modules\Catalog\Application\UseCases\CreateProduct\CreateProductInput;
use App\Modules\Catalog\Application\UseCases\CreateProduct\CreateProductUseCase;
use App\Modules\Catalog\Domain\Entities\Product;
use App\Modules\Catalog\Domain\Repositories\ProductRepository;
use App\Modules\Catalog\Domain\ValueObjects\Sku;
use App\Modules\Catalog\Domain\ValueObjects\TenantId;

final class InMemoryProductRepository implements ProductRepository
{
    /** @var array<string, Product> */
    public array $products = [];

    public function existsBySku(TenantId $tenantId, Sku $sku): bool
    {
        return isset($this->products[$tenantId->value . ':' . $sku->value]);
    }

    public function save(Product $product): void
    {
        $this->products[$product->tenantId()->value . ':' . $product->sku()->value] = $product;
    }
}

it('cadastra um produto para uma oficina', function (): void {
    $repository = new InMemoryProductRepository();
    $useCase = new CreateProductUseCase($repository);

    $output = $useCase->execute(new CreateProductInput(
        tenantId: 'tenant-1',
        name: 'Filtro de oleo',
        sku: 'FLT-001',
        barcode: '789000000001',
        category: 'Filtros',
        brand: 'Tecfil',
        supplier: 'Fornecedor A',
        minimumStock: 5,
        costInCents: 2590,
    ));

    expect($output->name)->toBe('Filtro de oleo');
    expect($output->sku)->toBe('FLT-001');
    expect($repository->products)->toHaveCount(1);
});
```

## 18. Pontos Para Analise Antes De Implementar

### 18.1 Product Deve Ter Estoque Atual?

No documento de produto, o cadastro inclui estoque atual. Pela arquitetura DDD, ha duas opcoes:

- Simples: manter `current_stock` em `products` no MVP.
- Mais correta: separar saldo em `inventory_items`.

Recomendacao:

Usar `inventory_items` para saldo e manter Product como cadastro. Isso separa Catalog de Inventory.

### 18.2 Categoria, Marca E Fornecedor Devem Ser Texto Ou Entidade?

Para o MVP:

- Categoria pode comecar como texto ou tabela simples.
- Marca pode comecar como texto.
- Fornecedor pode comecar como texto.

Quando compras e fornecedores avancarem, fornecedor deve virar entidade propria.

### 18.3 Codigo De Barras Deve Ser Unico?

Recomendacao:

- SKU deve ser unico por tenant.
- Codigo de barras pode ser unico por tenant quando informado, mas isso deve ser validado com casos reais.

Algumas oficinas podem cadastrar o mesmo codigo de barras para variantes ou kits, entao a decisao precisa ser confirmada.

### 18.4 Onde Criar Saldo Inicial?

Ao cadastrar produto, ha duas opcoes:

- Criar produto sem saldo e exigir entrada posterior.
- Permitir estoque inicial e gerar uma movimentacao de entrada.

Recomendacao:

Permitir estoque inicial, mas sempre registrar uma movimentacao de entrada para manter historico.

Esse comportamento pertence ao caso de uso de aplicacao, envolvendo Catalog e Inventory.

### 18.5 Presenter Ou Resource?

Para este caso de uso, a recomendacao e usar `CreateProductPresenter`.

Motivo:

- O use case retorna `CreateProductOutput`.
- O controller nao deve montar a resposta manualmente.
- A formatacao da resposta HTTP pertence a camada `Interfaces`.

`ProductResource` pode ser usado em consultas simples, principalmente em listagens, mas o caminho preferencial para comandos e casos de uso transacionais e `Presenter`.

## 19. Conclusao Do Exemplo

Este exemplo mostra a separacao esperada:

- Controller lida com HTTP.
- Use Case orquestra o fluxo.
- Entidade protege invariantes.
- Repositorio expressa necessidade do dominio.
- Eloquent fica isolado na Infrastructure.

Para implementar o MVP, o proximo passo recomendado e criar o esqueleto Laravel com os modulos `Identity`, `Catalog` e `Inventory`, iniciando por `UC04 - Cadastrar Produto/Peca`.

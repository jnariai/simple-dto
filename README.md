# Simple DTO

Immutable Data Transfer Object (DTO) for PHP inspired by [Laravel Data](https://github.com/spatie/laravel-data) and [Bags](https://github.com/dshafik/bag)

## Introduction

This is a package to create minimal immutable Data Transfer Object for PHP with some handy features like the method from to create the dto from array, it implements `Arrayable` and `Jsonable` interfaces, and also has two attributes to use `#[NonNullOutput]` and `#[Hidden]` to help in the output transformation.

## Installation

You can install the package via composer:

```bash
composer require jnariai/simple-dto
```
## Usage

```php
use SimpleDTO\DTO;

final readonly class MyDTO extends DTO
{
    public function __construct(
        public bool $property_bool,
        public ?string $property_string,
        public string $sensitive_data,
    ) {}
}
```

You can create the DTO using the static function `from::` and pass an array of data

```php
$myDTO = MyDTO::from([
    'property_bool' => true,
    'sensitive_data' => 'sensitive data',
]);

// You don't need to pass properties that can be null, it will automatically be set to null
/* MyDTO {
  +property_bool: true
  +property_string: null
  +sensitive_data: "sensitive data"
}
*/ 
```

You can also get a toArray an toJson method

```php
$myDTO->toArray(); // ['property_bool' => true, 'property_string' => null, 'sensitive_data' => 'sensitive data']
$myDTO->toJson(); // {"property_bool":true,"property_string":null,"sensitive_data":"sensitive data"}
```

You also get two available attributes to use. `#[NonNullOutput]` and `#[Hidden]`.

`#[NonNullOutput]` is a class Attribute and when using toArray or toJson it will only return properties that are not null.

```php
use SimpleDTO\DTO;

#[NonNullOutput]
final readonly class MyDTO extends DTO
{
    public function __construct(
        public bool $property_bool,
        public ?string $property_string,
        public string $sensitive_data,
    ) {}
}

$myDTO->toArray(); // ['property_bool' => true, 'sensitive_data' => 'sensitive data']
$myDTO->toJson(); // {"property_bool":true,"sensitive_data":"sensitive data"}
```

`#[Hidden]` is a property Attribute and when using toArray or toJson it will not return the property.

```php
use SimpleDTO\DTO;

final readonly class MyDTO extends DTO
{
    public function __construct(
    public bool $property_bool,
    public ?string $property_string,
    #[Hidden]
    public string $sensitive_data,
    ) {}
}

$myDTO->toArray(); // ['property_bool' => true, 'property_string' => null]
$myDTO->toJson(); // {"property_bool":true,"property_string":null}
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

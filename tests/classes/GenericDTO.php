<?php

namespace SimpleDTO\Tests\classes;

use SimpleDTO\DTO;

final readonly class GenericDTO extends DTO
{
    public function __construct(
        public bool $property_bool,
        public ?string $property_string,
        public ?array $property_array,
        public ?object $property_object,
        public ?int $property_int,
        public ?GenericNestedDTO $generic_nested_dto,
        public ?GenericClass $generic_class,
    ) {}
}

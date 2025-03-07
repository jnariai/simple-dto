<?php

declare(strict_types=1);

namespace SimpleDTO\Tests\classes;

use SimpleDTO\DTO;

final readonly class GenericNestedDTO extends DTO
{
    public function __construct(
        public string $property_string,
    ) {}
}

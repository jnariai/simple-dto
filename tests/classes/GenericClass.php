<?php

declare(strict_types=1);

namespace SimpleDTO\Tests\classes;

final readonly class GenericClass
{
    public function __construct(
        public string $property_string = 'generic class string',
    ) {}
}

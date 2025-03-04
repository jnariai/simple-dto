<?php

namespace SimpleDTO\Tests\classes;

use SimpleDTO\Attributes\Hidden;
use SimpleDTO\Attributes\NonNullOutput;
use SimpleDTO\DTO;

#[NonNullOutput]
final readonly class GenericDTOWithAttributes extends DTO
{
    public function __construct(
        public string $property_string,
        public ?string $property_string_nullable,
        #[Hidden]
        public string $property_string_hidden = 'default',
    ) {}
}

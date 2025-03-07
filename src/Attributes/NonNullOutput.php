<?php

declare(strict_types=1);

namespace SimpleDTO\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class NonNullOutput {}

<?php

declare(strict_types=1);

use SimpleDTO\Tests\classes\GenericClass;
use SimpleDTO\Tests\classes\GenericDTO;
use SimpleDTO\Tests\classes\GenericDTOWithAttributes;
use SimpleDTO\Tests\classes\GenericNestedDTO;

it('create DTO with required parameters only', function () {
    $genericDto = GenericDTO::from([
        'property_bool' => true,
    ]);

    expect($genericDto)->toBeInstanceOf(GenericDTO::class)
        ->and($genericDto->property_bool)->toBeTrue()
        ->and($genericDto->property_string)->toBeNull()
        ->and($genericDto->property_array)->toBeNull()
        ->and($genericDto->property_object)->toBeNull()
        ->and($genericDto->property_int)->toBeNull()
        ->and($genericDto->generic_nested_dto)->toBeNull()
        ->and($genericDto->generic_class)->toBeNull()
        ->and(get_object_vars($genericDto))->toHaveCount(7);
});

it('dto with required only, execute toArray method successfully', function () {
    $genericDto = GenericDTO::from([
        'property_bool' => true,
    ]);

    expect($genericDto->toArray())->toBe([
        'property_bool'      => true,
        'property_string'    => null,
        'property_array'     => null,
        'property_object'    => null,
        'property_int'       => null,
        'generic_nested_dto' => null,
        'generic_class'      => null,
    ]);
});

it('dto with required only, execute toJson method successfully', function () {
    $genericDto = GenericDTO::from([
        'property_bool' => true,
    ]);

    expect($genericDto->toJson())
        ->toBe('{"property_bool":true,"property_string":null,"property_array":null,"property_object":null,"property_int":null,"generic_nested_dto":null,"generic_class":null}');
});

it('create DTO with all parameters', function () {
    $genericDto = GenericDTO::from([
        'property_bool'      => true,
        'property_string'    => 'string',
        'property_array'     => ['array'],
        'property_object'    => (object) ['object'],
        'property_int'       => 1,
        'generic_nested_dto' => [
            'property_string' => 'nested dto string',
        ],
        'generic_class' => [
            'property_string' => 'class string',
        ],
    ]);

    expect($genericDto)->toBeInstanceOf(GenericDTO::class)
        ->and($genericDto->property_bool)->toBeTrue()
        ->and($genericDto->property_string)->toBe('string')
        ->and($genericDto->property_array)->toBe(['array'])
        ->and($genericDto->property_object)->toBeObject()
        ->and($genericDto->property_int)->toBe(1)
        ->and($genericDto->generic_nested_dto)->toBeInstanceOf(GenericNestedDTO::class)
        ->and($genericDto->generic_nested_dto->property_string)->toBe('nested dto string')
        ->and($genericDto->generic_class)->toBeInstanceOf(GenericClass::class)
        ->and($genericDto->generic_class->property_string)->toBe('class string')
        ->and(get_object_vars($genericDto))->toHaveCount(7);
});

it('execute toArray with attribute NonNullOutput', function () {
    $genericDto = GenericDTOWithAttributes::from([
        'property_string' => 'string',
    ]);

    expect($genericDto->toArray())->toBe([
        'property_string' => 'string',
    ]);
});

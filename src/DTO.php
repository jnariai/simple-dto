<?php

declare(strict_types=1);

namespace SimpleDTO;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionProperty;

/**
 * @implements Arrayable<string, mixed>
 */
abstract readonly class DTO implements Arrayable, Jsonable
{
    /**
     * @param  array<string, mixed>  $data
     *
     * @throws ReflectionException
     */
    final public static function from(array $data = []): static
    {
        $reflection = new ReflectionClass(static::class);
        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            return $reflection->newInstance();
        }

        $arguments = [];

        foreach ($constructor->getParameters() as $parameter) {
            $arguments[$parameter->getName()] = self::resolveParameterValue($parameter, $data);
        }

        return $reflection->newInstanceArgs($arguments);
    }

    final public function toArray(): array
    {
        $reflection = new ReflectionClass(static::class);
        $array = [];
        $skipNulls = $reflection->getAttributes('SimpleDTO\Attributes\NonNullOutput') !== [];

        foreach ($reflection->getProperties() as $property) {
            if (! $this->shouldIncludeProperty($property, $skipNulls)) {
                continue;
            }

            $value = $property->getValue($this);
            $array[$property->getName()] = $this->convertValueToArray($value);
        }

        return $array;
    }

    final public function toJson($options = 0): ?string
    {
        return json_encode($this->toArray(), $options) ?: null;
    }

    /**
     * @param  array<string, mixed>  $data
     *
     * @throws ReflectionException
     */
    private static function resolveParameterValue(ReflectionParameter $parameter, array $data): mixed
    {
        $parameterName = $parameter->getName();
        $value = $data[$parameterName] ?? null;

        if ($value === null) {
            if ($parameter->isDefaultValueAvailable()) {
                return $parameter->getDefaultValue();
            }

            if ($parameter->allowsNull()) {
                return null;
            }

            throw new InvalidArgumentException(
                "Missing required parameter '{$parameterName}' for DTO ".static::class
            );
        }

        $parameterType = $parameter->getType();

        if ((! $parameterType instanceof ReflectionNamedType) || $parameterType->isBuiltin()) {
            return $value;
        }

        $typeName = $parameterType->getName();
        if (is_subclass_of($typeName, self::class)) {
            if (! is_array($value)) {
                throw new InvalidArgumentException(
                    "Invalid value for parameter '{$parameterName}' in DTO ".static::class
                );
            }

            $nonStringKeys = array_filter(array_keys($value), fn ($key): bool => ! is_string($key));
            if ($nonStringKeys !== []) {
                throw new InvalidArgumentException(
                    "Invalid array keys for parameter '{$parameterName}' in DTO ".static::class.'. Expected string keys.'
                );
            }

            /** @var array<string, mixed> $value */
            return $typeName::from($value);
        }

        return class_exists($typeName)
            ? new $typeName(...$value)
            : $value;
    }

    private function shouldIncludeProperty(ReflectionProperty $property, bool $skipNulls): bool
    {
        if ($property->getAttributes('SimpleDTO\Attributes\Hidden') !== []) {
            return false;
        }

        return ! ($skipNulls && $property->getValue($this) === null);
    }

    private function convertValueToArray(mixed $value): mixed
    {
        if ($value instanceof self) {
            return $value->toArray();
        }

        if (is_object($value)) {
            $jsonEncode = json_encode($value) ?: '';

            return json_decode($jsonEncode, true);
        }

        return $value;
    }
}

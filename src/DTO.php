<?php

namespace SimpleDTO;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use InvalidArgumentException;
use ReflectionClass;

abstract readonly class DTO implements Arrayable, Jsonable
{
    public static function from(array $data = []): static
    {
        $reflection = new ReflectionClass(static::class);
        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            return new static;
        }

        $arguments = [];
        foreach ($constructor->getParameters() as $parameter) {
            $parameterName = $parameter->getName();
            $parameterType = $parameter->getType();

            if (! array_key_exists($parameterName, $data)) {
                if ($parameter->isDefaultValueAvailable()) {
                    $arguments[$parameterName] = $parameter->getDefaultValue();

                    continue;
                }

                if ($parameter->allowsNull()) {
                    $arguments[$parameterName] = null;

                    continue;
                }

                throw new InvalidArgumentException(
                    "Missing required parameter '{$parameterName}' for DTO ".static::class
                );
            }

            if ($parameterType === null || $parameterType->isBuiltin()) {
                $arguments[$parameterName] = $data[$parameterName];

                continue;
            }

            if (is_subclass_of($parameterType->getName(), DTO::class)) {
                $arguments[$parameterName] = $parameterType->getName()::from($data[$parameterName]);

                continue;
            }

            if (class_exists($parameterType->getName())) {
                $arguments[$parameterName] = new ($parameterType->getName())(...$data[$parameterName]);

                continue;
            }

            $arguments[$parameterName] = $data[$parameterName];
        }

        return $reflection->newInstanceArgs($arguments);
    }

    public function toArray(): array
    {
        $reflection = new ReflectionClass(static::class);
        $properties = $reflection->getProperties();
        $array = [];
        $nonNullOutput = $reflection->getAttributes('SimpleDTO\Attributes\NonNullOutput');

        foreach ($properties as $property) {
            $hidden = $property->getAttributes('SimpleDTO\Attributes\Hidden');

            if (! empty($hidden)) {
                continue;
            }

            if (! empty($nonNullOutput) && $property->getValue($this) === null) {
                continue;
            }

            if ($property->getValue($this) instanceof DTO) {
                $array[$property->getName()] = $property->getValue($this)->toArray();

                continue;
            }

            if (is_object($property->getValue($this))) {
                $array[$property->getName()] = json_decode(json_encode($property->getValue($this)), true);

                continue;
            }

            $array[$property->getName()] = $property->getValue($this);
        }

        return $array;
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }
}

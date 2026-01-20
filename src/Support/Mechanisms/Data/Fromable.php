<?php

declare(strict_types=1);

namespace SamLev\Acl\Support\Mechanisms\Data;

trait Fromable
{
    /**
     * @return class-string[]
     */
    protected static function getFromableClass(?\ReflectionType $type): array
    {
        if (
            $type instanceof \ReflectionNamedType
            && ! $type->isBuiltin()
            && class_exists($type->getName())
            && in_array(Fromable::class, class_uses_recursive($type->getName()))
        ) {
            return [$type->getName()];
        } elseif ($type instanceof \ReflectionIntersectionType || $type instanceof \ReflectionUnionType) {
            return array_unique(array_merge(...array_map(
                self::getFromableClass(...),
                $type->getTypes(),
            )));
        }

        return [];
    }

    /**
     * @param  array<array-key, mixed>  $payload
     */
    protected static function extractFromableData(\ReflectionParameter|\ReflectionProperty $param, array $payload): mixed
    {
        $data = match (true) {
            isset($payload[$param->getName()]) => $payload[$param->getName()],
            $param instanceof \ReflectionParameter && $param->isOptional() => $param->getDefaultValue(),
            $param instanceof \ReflectionProperty && $param->hasDefaultValue() => $param->getDefaultValue(),
            $param->getType()?->allowsNull() => null,
            $param->getType() instanceof \ReflectionNamedType && ! $param->getType()->isBuiltin() => app($param->getType()->getName()),
            default => throw new \InvalidArgumentException(
                sprintf('Missing required property "%s" of %s', $param->getName(), static::class)
            ),
        };

        if ($types = self::getFromableClass($param->getType())) {
            foreach ($types as $class) {
                try {
                    return $class::from($data);
                } catch (\InvalidArgumentException) {
                }
            }
        }

        return $data;
    }

    /**
     * @param  object|array<array-key, mixed>|string|null  $payload
     */
    public static function from(array|object|string|null $payload = null): static
    {
        $self = new \ReflectionClass(static::class);

        /** @var array<array-key, mixed> $decoded */
        $decoded = match (true) {
            is_string($payload) => json_decode($payload, true),
            is_object($payload) => (array) $payload,
            default => $payload,
        } ?? [];

        $constructor = [];

        if ($params = $self->getConstructor()?->getParameters()) {
            foreach ($params as $param) {
                $constructor[$param->getName()] = self::extractFromableData($param, $decoded);
            }
        }

        $instance = $self->newInstance(...$constructor);

        foreach ($self->getProperties(\ReflectionProperty::IS_PUBLIC) as $prop) {
            if ($prop->isStatic() || $prop->isReadOnly() || $prop->isPromoted()) {
                continue;
            }

            $prop->setValue(
                $instance,
                self::extractFromableData($prop, $decoded),
            );
        }

        return $instance;
    }
}

<?php

namespace SamLev\Acl\Permissions;

abstract class BasePermission
{
    public static function from(BasePermission|array|string|null $payload): static
    {

    }

    public static function default(): array
    {
        $base = new \ReflectionClass(static::class);
        $properties = array_filter(
            $base->getProperties(\ReflectionProperty::IS_PUBLIC),
            function ($property) {
                $type = $property->getType();

                if ($type instanceof \ReflectionNamedType && $type->isBuiltin() && $type->getName() === 'bool') {
                    return true;
                }

                return false;
            }
        );
    }
}

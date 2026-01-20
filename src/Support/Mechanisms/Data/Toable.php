<?php

declare(strict_types=1);

namespace SamLev\Acl\Support\Mechanisms\Data;

trait Toable
{
    /**
     * @return array<array-key, mixed>
     */
    public function toArray(): array
    {
        if ($this instanceof \JsonSerializable) {
            return (array) $this->jsonSerialize();
        }

        $reflection = new \ReflectionObject($this);
        $result = [];

        foreach ($reflection->getProperties(\ReflectionProperty::IS_PUBLIC) as $prop) {
            $result[$prop->getName()] = $prop->getValue($this);

            if (is_object($result[$prop->getName()])) {
                $result[$prop->getName()] = match (true) {
                    method_exists($result[$prop->getName()], 'toArray') => $result[$prop->getName()]->toArray(),
                    $result[$prop->getName()] instanceof \JsonSerializable => $result[$prop->getName()]->jsonSerialize(),
                    default => (array) $result[$prop->getName()],
                };
            }
        }

        return $result;
    }

    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    public function toString(): string
    {
        return $this->toJson();
    }
}

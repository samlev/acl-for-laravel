<?php

namespace SamLev\Acl\Support\Contracts;

interface NamedRole
{
    /**
     * @return NamedPermission[]
     */
    public function grants(): array;
}

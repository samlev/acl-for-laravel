<?php

namespace SamLev\Acl\Support\Contracts;

interface NamedRole
{
    /**
     * @return array<string, array<string, bool>>
     */
    public function grants(): array;
}

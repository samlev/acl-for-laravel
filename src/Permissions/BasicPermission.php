<?php

namespace SamLev\Acl\Permissions;

class BasicPermission extends BasePermission
{
    public function __construct(
        public bool $view = false,
        public bool $update = false,
    ) {
        //
    }
}

<?php

namespace SamLev\Acl\Permissions;

class CrudPermission extends BasePermission
{
    public function __construct(
        public bool $create = false,
        public bool $read = false,
        public bool $update = false,
        public bool $delete = false,
    ) {
        //
    }
}

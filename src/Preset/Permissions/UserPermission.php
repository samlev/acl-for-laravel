<?php

namespace SamLev\Acl\Preset\Permissions;

use SamLev\Acl\Permissions\BasePermission;
use SamLev\Acl\Permissions\BasicPermission;

class UserPermission extends BasePermission
{
    public BasicPermission $users;
}

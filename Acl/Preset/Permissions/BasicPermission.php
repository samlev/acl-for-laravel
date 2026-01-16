<?php

namespace SamLev\Acl\Preset\Permissions;

use SamLev\Acl\Support\Contracts\NamedPermission;

enum BasicPermission: string implements NamedPermission
{
    case view = 'view';
    case update = 'update';
}

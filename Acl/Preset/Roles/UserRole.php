<?php

namespace SamLev\Acl\Preset\Roles;

use SamLev\Acl\Preset\Permissions\BasicPermission;
use SamLev\Acl\Support\Contracts\NamedRole;

enum UserRole: string implements NamedRole
{
    case admin = 'admin';
    case user = 'user';

    public function grants(): array
    {
        return match ($this) {
            self::admin => BasicPermission::cases(),
            self::user => [
                BasicPermission::view,
            ],
        };
    }
}

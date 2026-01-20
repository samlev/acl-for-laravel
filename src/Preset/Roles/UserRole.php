<?php

namespace SamLev\Acl\Preset\Roles;

use SamLev\Acl\Support\Contracts\NamedRole;

enum UserRole: string implements NamedRole
{
    case admin = 'admin';
    case user = 'user';

    public function grants(): array
    {
        return match ($this) {
            self::admin => [
                'users' => [
                    'view' => true,
                    'update' => true,
                ],
            ],
            self::user => [
                'users' => [
                    'view' => true,
                ],
            ],
        };
    }
}

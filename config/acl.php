<?php

return [
    'defaults' => [
        'guard' => 'web',
    ],

    'guards' => [
        'web' => [
            'roles' => [
                SamLev\Acl\Preset\Roles\UserRole::class,
            ],
            'permissions' => [
                SamLev\Acl\Preset\Permissions\UserPermission::class,
            ],
        ],
    ],

    'database' => [
        'roles_table' => 'user_role',
        'grants_table' => 'user_permission',
        'with_scopes' => false,
        'with_polymorphic_scopes' => false,
    ],
];

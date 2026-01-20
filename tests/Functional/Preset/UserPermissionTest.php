<?php

use SamLev\Acl\Permissions\BasicPermission;
use SamLev\Acl\Preset\Permissions\UserPermission;
use SamLev\Acl\Preset\Roles\UserRole;

it('builds nested users permission from payload', function (array|object|string $payload) {
    $perm = UserPermission::from($payload);

    expect($perm->users)->toBeInstanceOf(BasicPermission::class)
        ->and($perm->users->view)->toBeTrue()
        ->and($perm->users->update)->toBeTrue()
        ->and($perm->toArray())->toBe(['users' => ['view' => true, 'update' => true]]);
})->with([
    'array' => [['users' => ['view' => true, 'update' => true]]],
    'json' => ['{"users":{"view":true,"update":true}}'],
    'object' => [(object) ['users' => (object) ['view' => true, 'update' => true]]],
]);

it('applies grants from UserRole cases', function () {
    $admin = UserPermission::from(UserRole::admin->grants());
    expect($admin->users)->toBeInstanceOf(BasicPermission::class)
        ->and($admin->users->view)->toBeTrue()
        ->and($admin->users->update)->toBeTrue();

    $user = UserPermission::from(UserRole::user->grants());
    expect($user->users)->toBeInstanceOf(BasicPermission::class)
        ->and($user->users->view)->toBeTrue()
        ->and($user->users->update)->toBeFalse();
});

it('ignores irrelevant permission keys for nested users', function (array|object|string $payload) {
    $perm = UserPermission::from($payload);

    // delete is not a BasicPermission key and should be ignored
    expect($perm->users->view)->toBeTrue()
        ->and($perm->users->update)->toBeFalse()
        ->and($perm->toArray())->toBe(['users' => ['view' => true, 'update' => false]])
        ->and($perm->toJson())->toBe('{"users":{"view":true,"update":false}}');
})->with([
    'array' => [['users' => ['view' => true, 'delete' => true, 'update' => false]]],
    'json' => ['{"users":{"view":true,"delete":true,"update":false}}'],
    'object' => [(object) ['users' => (object) ['view' => true, 'delete' => true, 'update' => false]]],
]);

it('handles an empty payload', function () {
    $perm = UserPermission::from();

    expect($perm->users->view)->toBeFalse()
        ->and($perm->users->update)->toBeFalse()
        ->and($perm->toArray())->toBe(['users' => ['view' => false, 'update' => false]])
        ->and($perm->toJson())->toBe('{"users":{"view":false,"update":false}}');
});

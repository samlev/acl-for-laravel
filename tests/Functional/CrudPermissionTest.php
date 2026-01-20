<?php

use SamLev\Acl\Permissions\CrudPermission;

it('constructs with default values', function () {
    $perm = new CrudPermission;
    expect($perm->create)->toBeFalse()
        ->and($perm->read)->toBeFalse()
        ->and($perm->update)->toBeFalse()
        ->and($perm->delete)->toBeFalse();
});

it('builds from payload', function (array|object|string $payload) {
    $perm = CrudPermission::from($payload);

    expect($perm->create)->toBeTrue()
        ->and($perm->read)->toBeTrue()
        ->and($perm->update)->toBeTrue()
        ->and($perm->delete)->toBeTrue();
})->with([
    'array' => [['create' => true, 'read' => true, 'update' => true, 'delete' => true]],
    'json' => ['{"create":true,"read":true,"update":true,"delete":true}'],
    'object' => [(object) ['create' => true, 'read' => true, 'update' => true, 'delete' => true]],
]);

it('toArray returns public properties', function () {
    $perm = new CrudPermission(create: true, read: false, update: true, delete: false);
    expect($perm->toArray())->toBe([
        'create' => true,
        'read' => false,
        'update' => true,
        'delete' => false,
    ]);
});

it('toJson returns JSON', function () {
    $perm = new CrudPermission(create: true, read: true, update: true, delete: true);
    expect($perm->toJson())->toBe('{"create":true,"read":true,"update":true,"delete":true}');
});

it('ignores irrelevant permission keys', function (array|object|string $payload) {
    $perm = CrudPermission::from(['create' => true, 'archive' => true, 'read' => false]);

    expect($perm->create)->toBeTrue()
        ->and($perm->read)->toBeFalse()
        ->and($perm->update)->toBeFalse()
        ->and($perm->delete)->toBeFalse()
        ->and($perm->toArray())->toBe([
            'create' => true,
            'read' => false,
            'update' => false,
            'delete' => false,
        ])
        ->and($perm->toJson())->toBe('{"create":true,"read":false,"update":false,"delete":false}');
})->with([
    'array' => [['create' => true, 'archive' => true, 'read' => false]],
    'json' => ['{"create":true,"archive":true,"read":false}'],
    'object' => [(object) ['create' => true, 'archive' => true, 'read' => false]],
]);

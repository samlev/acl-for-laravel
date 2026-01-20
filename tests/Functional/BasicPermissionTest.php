<?php

use SamLev\Acl\Permissions\BasicPermission;

it('constructs with default values', function () {
    $perm = new BasicPermission;
    expect($perm->view)->toBeFalse()
        ->and($perm->update)->toBeFalse();
});

it('builds from payload', function (array|object|string $payload) {
    $perm = BasicPermission::from($payload);

    expect($perm->view)->toBeTrue()
        ->and($perm->update)->toBeTrue();
})->with([
    'array' => [['view' => true, 'update' => true]],
    'json' => ['{"view":true,"update":true}'],
    'object' => [(object) ['view' => true, 'update' => true]],
]);

it('toArray returns public properties', function () {
    $perm = new BasicPermission(view: true, update: false);
    expect($perm->toArray())->toBe(['view' => true, 'update' => false]);
});

it('toJson returns JSON', function () {
    $perm = new BasicPermission(view: true, update: true);
    expect($perm->toJson())->toBe('{"view":true,"update":true}');
});

it('ignores irrelevant permission keys', function (array|object|string $payload) {
    $perm = BasicPermission::from(['view' => true, 'delete' => true]);

    expect($perm->view)->toBeTrue()
        ->and($perm->update)->toBeFalse()
        ->and($perm->toArray())->toBe(['view' => true, 'update' => false])
        ->and($perm->toJson())->toBe('{"view":true,"update":false}');
})->with([
    'array' => [['view' => true, 'delete' => true]],
    'json' => ['{"view":true,"delete":true}'],
    'object' => [(object) ['view' => true, 'delete' => true]],
]);

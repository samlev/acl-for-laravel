<?php

use SamLev\Acl\Support\Mechanisms\Data\Toable;

it('only includes public properties in toArray', function () {
    // class with public, protected and private properties
    $obj = new class
    {
        use Toable;

        public string $visible = 'yes';
        protected string $hidden = 'no';
        private string $secret = 'no';
    };

    expect($obj->toArray())->toBe(['visible' => 'yes']);
});

it('uses jsonSerialize result when object implements JsonSerializable', function () {
    $obj = new class implements JsonSerializable
    {
        use Toable;

        public string $a = 'original';

        public function jsonSerialize(): array
        {
            return ['overridden' => 42];
        }
    };

    expect($obj->toArray())->toBe(['overridden' => 42]);
});

it('recursively converts nested objects that provide toArray (trait or method)', function () {
    // child uses Toable
    $child = new class
    {
        use Toable;

        public int $value = 7;
    };

    // container with a promoted public property
    $container = new class ($child)
    {
        use Toable;

        public function __construct(public $child) {}
    };

    expect($container->toArray())->toBe(['child' => ['value' => 7]]);

    // test with an inner object that defines a toArray method (not using the trait)
    $customInner = new class
    {
        public function toArray(): array
        {
            return ['ok' => true];
        }
    };

    $wrapper = new class ($customInner)
    {
        use Toable;

        public function __construct(public $inner) {}
    };

    expect($wrapper->toArray())->toBe(['inner' => ['ok' => true]]);
});

it('converts nested objects that implement JsonSerializable using jsonSerialize', function () {
    $inner = new class implements JsonSerializable
    {
        public function jsonSerialize(): array
        {
            return ['js' => 5];
        }
    };

    $holder = new class ($inner)
    {
        use Toable;

        public function __construct(public $inner) {}
    };

    expect($holder->toArray())->toBe(['inner' => ['js' => 5]]);
});

it('casts unknown inner objects to arrays via (array) cast', function () {
    $inner = (object) ['a' => 1, 'b' => 2];

    $holder = new class ($inner)
    {
        use Toable;

        public function __construct(public $inner) {}
    };

    expect($holder->toArray())->toBe(['inner' => ['a' => 1, 'b' => 2]]);
});

it('toJson returns JSON', function () {
    $obj = new class
    {
        use Toable;

        public string $u = 'ü/😊';
    };

    $expected = json_encode(['u' => 'ü/😊'], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    expect($obj->toJson())->toBe($expected);
});

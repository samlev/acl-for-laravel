<?php

use SamLev\Acl\Support\Mechanisms\Data\Fromable;

class BooleanFromable
{
    use Fromable;
    public static bool $qua = true;
    public readonly bool $quo;

    public bool $foo;
    public bool $bar = true;
    public ?bool $baz;
    protected bool $fuzz;

    public function __construct(
        public bool $bing,
        public bool $bang = true,
        bool $fizz = true,
    ) {
        $this->quo = true;
        $this->fuzz = ! $fizz;
    }

    public function getFuzz(): bool
    {
        return $this->fuzz;
    }
}

class NestedFromable
{
    use Fromable;

    public BooleanFromable $foo;

    public function __construct(
        public BooleanFromable $bar,
    ) {
        //
    }
}

it('builds a nested object from a payload', function (array|object|string $payload) {
    $concrete = BooleanFromable::from($payload);

    expect($concrete)
        ->toHaveProperty('foo', true)
        ->toHaveProperty('bar', false)
        ->toHaveProperty('baz', true)
        ->toHaveProperty('quo', true)
        ->toHaveProperty('bing', true)
        ->toHaveProperty('bang', false)
        ->and($concrete::$qua)->toBeTrue()
        ->and($concrete->getFuzz())->toBeTrue();
})->with('fromable payload');

it('handles nested data', function (array|object|string $payload) {
    $concrete = NestedFromable::from([
        'foo' => $payload,
        'bar' => BooleanFromable::from($payload),
    ]);

    expect($concrete)
        ->toHaveProperty('foo')
        ->toHaveProperty('bar')
        ->and($concrete->foo)
        ->toHaveProperty('foo', true)
        ->toHaveProperty('bar', false)
        ->toHaveProperty('baz', true)
        ->toHaveProperty('quo', true)
        ->toHaveProperty('bing', true)
        ->toHaveProperty('bang', false)
        ->and($concrete->bar)
        ->toHaveProperty('foo', true)
        ->toHaveProperty('bar', false)
        ->toHaveProperty('baz', true)
        ->toHaveProperty('quo', true)
        ->toHaveProperty('bing', true)
        ->toHaveProperty('bang', false);
})->with('fromable payload');

it('requires constructor parameters', function () {
    BooleanFromable::from([]);
})->throws(\InvalidArgumentException::class, 'Missing required property "bing" of '.BooleanFromable::class);

it('requires non-default parameters', function () {
    BooleanFromable::from(['bing' => true]);
})->throws(\InvalidArgumentException::class, 'Missing required property "foo" of '.BooleanFromable::class);

class UnionA
{
    use Fromable;

    public function __construct(public string $onlyA) {}
}

class UnionB
{
    use Fromable;

    public function __construct(public string $onlyB) {}
}

class HolderUnion
{
    use Fromable;

    public function __construct(public UnionA|UnionB $child) {}
}

it('handles union typed constructor parameters by trying each Fromable type in order', function () {
    $holder = HolderUnion::from(['child' => ['onlyB' => 'value']]);

    expect($holder->child)->toBeInstanceOf(UnionB::class)
        ->and($holder->child->onlyB)->toBe('value');
});

interface MarkerForIntersect {}

class IntersectChild implements MarkerForIntersect
{
    use Fromable;

    public function __construct(public int $val) {}
}

class HolderIntersect
{
    use Fromable;

    public function __construct(public IntersectChild&MarkerForIntersect $child) {}
}

it('handles intersection typed constructor parameters by using the concrete Fromable class', function () {
    $holder = HolderIntersect::from(['child' => ['val' => 123]]);

    expect($holder->child)->toBeInstanceOf(IntersectChild::class)
        ->and($holder->child->val)->toBe(123);
});

dataset('fromable payload', [
    'array' => [
        [
            'foo' => true,
            'bar' => false,
            'baz' => true,
            'quo' => false,
            'qua' => false,
            'bing' => true,
            'bang' => false,
            'fizz' => false,
        ],
    ],
    'json' => [
        '{"foo":true,"bar":false,"baz":true,"quo":false,"qua":false,"bing":true,"bang":false,"fizz":false}',
    ],
    'object' => [
        (object) [
            'foo' => true,
            'bar' => false,
            'baz' => true,
            'quo' => false,
            'qua' => false,
            'bing' => true,
            'bang' => false,
            'fizz' => false,
        ],
    ],
]);

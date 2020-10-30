<?php

namespace Smartymoon\Inertia;

use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Arr;
use PHPUnit\Framework\Assert as PHPUnit;

class Assertions
{
    /**
     * 检查返回的组件名，和 props  Inertia::render(Companent, props)
     * @return Closure
     */
    public function assertInertia()
    {
        return function ($component = null, $props = []) {

            $content = $this->assertJsonStructure([
                'component',
                'props',
                'url',
                'version'
            ])->json();

            if (! is_null($component)) {
                PHPUnit::assertEquals($component, $content['component']);
            }

            $this->assertInertiaHasAll($props);

            return $this;
        };
    }

    public function assertInertiaHas()
    {
        return function ($key, $value = null) {
            if (is_array($key)) {
                return $this->assertInertiaHasAll($key);
            }

            if (is_null($value)) {
                PHPUnit::assertTrue(Arr::has($this->inertiaProps(), $key));
            } elseif ($value instanceof Closure) {
                PHPUnit::assertTrue($value($this->inertiaProps($key)));
            } elseif ($value instanceof Arrayable) {
                PHPUnit::assertEquals($value->toArray(), $this->inertiaProps($key));
            } elseif ($value instanceof Responsable) {
                PHPUnit::assertEquals($value->toResponse($this)->getData(), $this->inertiaProps($key));
            } else {
                PHPUnit::assertEquals($value, $this->inertiaProps($key));
            }

            return $this;
        };
    }

    public function assertInertiaHasAll()
    {
        return function (array $bindings) {
            foreach ($bindings as $key => $value) {
                if (is_int($key)) {
                    $this->assertInertiaHas($value);
                } else {
                    $this->assertInertiaHas($key, $value);
                }
            }

            return $this;
        };
    }

    public function assertInertiaMissing()
    {
        return function ($key) {
            $this->assertInertia();

            PHPUnit::assertFalse(Arr::has($this->inertiaProps(), $key));

            return $this;
        };
    }

    public function inertiaProps()
    {
        return function ($key = null) {
            $this->assertInertia();

            if (is_null($key)) {
                return $this->json()['props'];
            }

            return Arr::get($this->json()['props'], $key);
        };
    }
}

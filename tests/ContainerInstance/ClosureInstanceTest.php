<?php

declare(strict_types=1);

namespace flotzilla\Container\Test\ContainerInstance;

use ArgumentCountError;
use flotzilla\Container\ContainerInstance\ClosureInstance;
use flotzilla\Container\ContainerInstance\ContainerInstance;
use flotzilla\Container\Test\TestClass\EmptyTestClass;
use flotzilla\Container\Test\TestClass\TestClass;
use PHPUnit\Framework\TestCase;

class ClosureInstanceTest extends TestCase
{
    public function testConstruct()
    {
        $c = function () {
            return new EmptyTestClass();
        };

        $ci = new ClosureInstance($c);
        $this->assertInstanceOf(ClosureInstance::class, $ci);
        $this->assertEquals(ContainerInstance::TYPE_CLOSURE, $ci->getType());
    }

    public function testCall()
    {
        $c = function () {
            return new EmptyTestClass();
        };

        $ci = new ClosureInstance($c);
        $response = $ci->call();
        $this->assertInstanceOf(EmptyTestClass::class, $response);
    }

    public function testCallWithDependency()
    {
        $c = function () {
            return new TestClass('test');
        };

        $ci = new ClosureInstance($c);
        $response = $ci->call();
        $this->assertInstanceOf(TestClass::class, $response);
        $this->assertEquals('test', $response->getMessage());
    }

    public function testCallWithParams()
    {
        $c = function ($x) {
            return new TestClass($x);
        };

        $ci = new ClosureInstance($c);
        $response = $ci->callWithParameters(['test']);
        $this->assertInstanceOf(TestClass::class, $response);
        $this->assertEquals('test', $response->getMessage());
    }

    public function testParams()
    {
        $ci = new ClosureInstance(function ($x) {
            return new TestClass($x);
        });
        $ci->setParameters(['not test']);
        $this->assertEquals(['not test'], $ci->getParameters());
        $response = $ci->call();
        $this->assertInstanceOf(TestClass::class, $response);
        $this->assertEquals('not test', $response->getMessage());
    }

    public function testCallWithoutParams()
    {
        $this->expectException(ArgumentCountError::class);
        $ci = new ClosureInstance(function ($x) {
            return new TestClass($x);
        });
        $ci->call();
    }

    public function testCallWithParamsButWithout()
    {
        $this->expectException(ArgumentCountError::class);
        $ci = new ClosureInstance(function ($x) {
            return new TestClass($x);
        });
        $ci->call();
        $ci->callWithParameters();
    }
}

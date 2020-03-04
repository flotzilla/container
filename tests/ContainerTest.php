<?php

declare(strict_types=1);

namespace flotzilla\Container\Test;

use flotzilla\Container\Container;
use flotzilla\Container\Exceptions\ClassIsNotInstantiableException;
use flotzilla\Container\Exceptions\ContainerNotFoundException;
use flotzilla\Container\Exceptions\ContainerServiceInitializationException;
use flotzilla\Container\Test\TestClass\EmptyTestClass;
use flotzilla\Container\Test\TestClass\TestClass;
use flotzilla\Container\Test\TestClass\TestClassWithDependecy;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class ContainerTest extends TestCase
{
    protected $containers = [];

    protected function setUp()
    {
        $this->containers =             [
            'EmptyTestClass' => EmptyTestClass::class,
            'TestClassDI' => [TestClass::class, 'message'],
            'TestClassDI2' => function () {return new TestClass('test');},
            'TestClassWithDependecyDI' => [TestClassWithDependecy::class, 'TestClassDI']
        ];
    }

    public function testEmptyInit()
    {
        $container = new Container();
        $this->assertCount(0, $container);
    }

    public function testInit()
    {
        $container = new Container($this->containers);
        $this->assertCount(4, $container);
    }

    public function testHas()
    {
        $container = new Container($this->containers);
        $this->assertTrue($container->has('TestClassDI'));
        $this->assertTrue($container->has('TestClassDI2'));
        $this->assertTrue($container->has('EmptyTestClass'));
        $this->assertTrue($container->has('TestClassWithDependecyDI'));
    }

    public function testGet()
    {
        $container = new Container($this->containers);

        $emptyClass = $container->get('EmptyTestClass');
        $testClass = $container->get('TestClassDI');
        $testClass2 = $container->get('TestClassDI2');
        $testClassWithD = $container->get('TestClassWithDependecyDI');

        $this->assertInstanceOf(EmptyTestClass::class, $emptyClass);
        $this->assertInstanceOf(TestClass::class, $testClass);
        $this->assertInstanceOf(TestClass::class, $testClass2);
        $this->assertInstanceOf(TestClassWithDependecy::class, $testClassWithD);

        $this->assertEquals('message', $testClass->getMessage());
        $this->assertEquals('test', $testClass2->getMessage());
        $this->assertEquals($testClass, $testClassWithD->getTestClass());
        $this->assertEquals('message', $testClassWithD->getTestClass()->getMessage());
    }

    public function testGetClosureWithParameter()
    {
        $container = new Container(
            [
                'closureDI' => function ($y, $z) {return $z + $y;}
            ]
        );

        $result = $container->getWithParameters('closureDI', [1,2]);
        $this->assertEquals(3, $result);
    }

    public function testGetClassWithParameterError()
    {
        $this->expectException(ClassIsNotInstantiableException::class);
        $container = new Container(
            [
                'classDI' => EmptyTestClass::class
            ]
        );

        $result = $container->getWithParameters('classDI', ['test']);
    }

    public function testGetClassWithParameter()
    {
        $container = new Container(
            [
                'classDI' => TestClass::class
            ]
        );

        $result = $container->getWithParameters('classDI', ['test']);
        $this->assertInstanceOf(TestClass::class, $result);
        $this->assertEquals('test', $result->getMessage());
    }


    public function testGetClassWithParameterRewrite()
    {
        $container = new Container(
            [
                'classDI' => [TestClass::class, 'not test'],
            ]
        );

        $result = $container->getWithParameters('classDI', ['test']);
        $this->assertInstanceOf(TestClass::class, $result);
        $this->assertEquals('test', $result->getMessage());
    }
}

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
        $this->containers = [
            'EmptyTestClass' => EmptyTestClass::class,
            'TestClassDI' => [TestClass::class, 'message'],
            'TestClassDI2' => function () {
                return new TestClass('test');
            },
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
                'closureDI' => function ($y, $z) {
                    return $z + $y;
                }
            ]
        );

        $result = $container->getWithParameters('closureDI', [1, 2]);
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

    public function testSetMethodArrayDep()
    {
        $container = new Container();
        $container->set('EmptyServiceId', [EmptyTestClass::class]);
        $container->set('TestClassId', [TestClass::class, 'message']);
        $container->set('TestClosureId', function () {
            return new TestClass('test');
        });
        $container->set('TestDepId', [TestClassWithDependecy::class, 'TestClassId']);

        $result = $container->get('EmptyServiceId');
        $testClassResult = $container->get('TestClassId');
        $testClosureResult = $container->get('TestClosureId');
        $testDepClassResult = $container->get('TestDepId');

        $this->assertInstanceOf(EmptyTestClass::class, $result);
        $this->assertInstanceOf(TestClass::class, $testClassResult);
        $this->assertInstanceOf(TestClass::class, $testClosureResult);
        $this->assertInstanceOf(TestClassWithDependecy::class, $testDepClassResult);
        $this->assertEquals('message', $testDepClassResult->getTestClass()->getMessage());
    }

    public function testSetMethodException()
    {
        $this->expectException(ContainerServiceInitializationException::class);
        $container = new Container();
        $container->set('str', new \stdClass());
    }

    public function testSetMethodExceptionRewrite()
    {
        $id = 'testId';

        $container = new Container([
            $id => [TestClass::class, 'testParam1']
        ]);

        $this->assertEquals('testParam1', $container->get($id)->getMessage());
        $container->set($id, [TestClass::class, 'testParam2', true], true);
        $this->assertEquals('testParam2', $container->get($id)->getMessage());
    }

    public function testSetMethodRewriteError()
    {
        $this->expectException(ContainerServiceInitializationException::class);
        $this->expectExceptionMessage('Service testId is already in container stack');
        $id = 'testId';

        $container = new Container([
            $id => [TestClass::class, 'testParam1']
        ]);

        $this->assertEquals('testParam1', $container->get($id)->getMessage());
        $container->set($id, [TestClass::class, 'testParam2'], false);
    }

    public function testGetOnNonExistedService()
    {
        $this->expectException(ContainerNotFoundException::class);
        $this->expectExceptionMessage('Container 123 not found');
        $c = new Container();
        $c->get('123');
    }

    public function testListServices()
    {
        $containerIds = [
            'EmptyTestClass',
            'TestClassDI',
            'TestClassDI2',
            'TestClassWithDependecyDI',
        ];
        $ci = new Container($this->containers);
        $this->assertEquals($containerIds, $ci->listServiceIds());
    }

    public function testInitFromArrayException()
    {
        $this->expectException(ContainerServiceInitializationException::class);
        new Container([
           'testDi' => new \stdClass()
        ]);
    }

    public function testInitFromArrayparamException()
    {
        $this->expectException(ContainerServiceInitializationException::class);
        $this->expectExceptionMessage('Service testDi 123 parameter should be string');
        new Container([
            'testDi' => [123]
        ]);
    }

    public function testGetWithParameters()
    {
        $this->expectException(ContainerNotFoundException::class);
        $this->expectExceptionMessage('Container testDIWrong not found');
        $c = new Container([
            'testDI' => [TestClass::class]
        ]);

        $c->getWithParameters('testDIWrong', ['test message']);
    }
}

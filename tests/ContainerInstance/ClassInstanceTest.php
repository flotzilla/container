<?php

namespace flotzilla\Container\Test\ContainerInstance;

use flotzilla\Container\ContainerInstance\ClassInstance;
use flotzilla\Container\ContainerInstance\ContainerInstance;
use flotzilla\Container\Exceptions\ClassIsNotInstantiableException;
use flotzilla\Container\Test\TestClass\AbstractTestClass;
use flotzilla\Container\Test\TestClass\TestClass;
use flotzilla\Container\Test\TestClass\EmptyTestClass;
use PHPUnit\Framework\TestCase;
use ReflectionException;

class ClassInstanceTest extends TestCase
{
    /**
     * @return ClassInstance
     * @throws \ReflectionException
     * @throws \flotzilla\Container\Exceptions\ClassIsNotInstantiableException
     */
    private function getBaseCI() : ClassInstance
    {
        $parameters = ['test1', 'test2'];
        $ci = new ClassInstance(TestClass::class, $parameters);

        return $ci;
    }

    public function testCreation()
    {
        $ci = $this->getBaseCI();

        $this->assertEquals(ContainerInstance::TYPE_CLASS, $ci->getType());
        $this->assertEquals('test1', $ci->getParameters()[0]);
        $this->assertEquals('test2', $ci->getParameters()[1]);
        $this->assertEquals(TestClass::class, $ci->getReflectionClass()->getName());
    }

    public function testNonExistExceptionOnCreation()
    {
        $this->expectException(ReflectionException::class);
        new ClassInstance('some str');
    }

    public function testExceptionOnCreation()
    {
        $this->expectException(ClassIsNotInstantiableException::class);
        new ClassInstance(AbstractTestClass::class);
    }

    public function testGettersSetters()
    {
        $ci = $this->getBaseCI();
        $ci->setParameters([]);

        $this->assertEquals([], $ci->getParameters());

        $ci->setParameters([123, 222]);

        $this->assertEquals(123, $ci->getParameters()[0]);
        $this->assertEquals(222, $ci->getParameters()[1]);
    }

    public function testConstructorCall(){
        $ci = new ClassInstance(TestClass::class, ['message text']);
        $object = $ci->call();
        $this->assertEquals(TestClass::class, get_class($object));
        $this->assertInstanceOf(TestClass::class, $object);
        $this->assertEquals('message text', $object->getMessage());
        $this->assertTrue($ci->hasConstructor());
    }

    public function testConstructorCallWithArguments(){
        $ci = new ClassInstance(TestClass::class);
        $object = $ci->callWithParameters(['message text']);
        $this->assertEquals(TestClass::class, get_class($object));
        $this->assertInstanceOf(TestClass::class, $object);
        $this->assertEquals('message text', $object->getMessage());
        $this->assertTrue($ci->hasConstructor());
    }

    public function testEmptyConstructorCallException()
    {
        $this->expectException(ClassIsNotInstantiableException::class);
        $this->expectExceptionMessage('Class flotzilla\Container\Test\TestClass\EmptyTestClass cannot be called with arguments');
        $ci = new ClassInstance(EmptyTestClass::class, ['message text']);
        $object = $ci->call();
    }

    public function testEmptyConstructorCall()
    {
        $ci = new ClassInstance(EmptyTestClass::class);
        $object = $ci->call();
        $this->assertEquals(EmptyTestClass::class, get_class($object));
        $this->assertInstanceOf(EmptyTestClass::class, $object);
        $this->assertFalse($ci->hasConstructor());
    }

    public function testEmptyConstructorCallWithParams()
    {
        $this->expectException(ClassIsNotInstantiableException::class);
        $this->expectExceptionMessage('Class flotzilla\Container\Test\TestClass\EmptyTestClass cannot be called with arguments');
        $ci = new ClassInstance(EmptyTestClass::class, ['message text']);
        $object = $ci->callWithParameters(['message text']);
        $this->assertEquals(EmptyTestClass::class, get_class($object));
        $this->assertInstanceOf(EmptyTestClass::class, $object);
        $this->assertFalse($ci->hasConstructor());
    }
}

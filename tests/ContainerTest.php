<?php

declare(strict_types=1);

namespace flotzilla\Container\Test;

use flotzilla\Container\Container;
use flotzilla\Container\Exceptions\ContainerNotFoundException;
use flotzilla\Container\Exceptions\ContainerServiceInitializationException;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    const PLAIN_OBJECT = 'PlainObject';
    private static $objectsStack = [];

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::$objectsStack = [
            self::PLAIN_OBJECT => function () {
                return new \stdClass();
            }
        ];
    }

    public function testInit()
    {
        $container = new Container(self::$objectsStack);

        $this->assertTrue($container->has(self::PLAIN_OBJECT));
    }

    public function testHasObject()
    {
        $container = new Container(self::$objectsStack);

        $this->assertTrue($container->has(self::PLAIN_OBJECT));
    }

    public function testHasObjectOnNonExisted()
    {
        $container = new Container(self::$objectsStack);

        $this->assertFalse($container->has('someval'));
    }

    public function testHasOnNullValue()
    {
        $container = new Container(self::$objectsStack);

        $this->assertFalse($container->has(null));
    }

    public function testListServices()
    {
        $container = new Container(self::$objectsStack);

        $this->assertTrue(in_array(self::PLAIN_OBJECT, $container->listServiceIds()));
    }

    public function testListServicesOnNonExisted()
    {
        $container = new Container(self::$objectsStack);

        $this->assertFalse(in_array('test', $container->listServiceIds()));
    }

    public function testListServicesOnEmptyStack()
    {
        $container = new Container();

        $this->assertFalse(in_array('test', $container->listServiceIds()));
    }

    public function testListServicesOnNonExistedEmptyStack()
    {
        $container = new Container(self::$objectsStack);

        $this->assertFalse(in_array('test', $container->listServiceIds()));
    }

    public function testInitWithoutClosable()
    {
        $this->expectException(ContainerServiceInitializationException::class);

        new Container(
            [
            self::PLAIN_OBJECT => 'wrong body'
            ]
        );
    }

    public function testSetter()
    {
        $this->expectException(\TypeError::class);

        $container = new Container();
        $container->set('test', 'tst');
    }

    public function testGetOnEmptyStack()
    {
        $this->expectException(ContainerNotFoundException::class);
        $container = new Container();
        $container->get('test');
    }

    public function testGetAfterSetMethod()
    {
        $value = 123;
        $container = new Container();
        $container->set(
            'test', function () use ($value) {
                return $value;
            }
        );
        $this->assertEquals($value, $container->get('test'));
    }

    public function testGetAfterSetMethodNonExisted()
    {
        $this->expectException(ContainerNotFoundException::class);

        $value = 123;
        $container = new Container();
        $container->set(
            'test', function () use ($value) {
                return $value;
            }
        );
        $this->assertEquals($value, $container->get('test1'));
    }
}

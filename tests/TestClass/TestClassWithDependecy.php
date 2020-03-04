<?php


namespace flotzilla\Container\Test\TestClass;


class TestClassWithDependecy
{

    /**
     * @var TestClass
     */
    private $testClass;

    /**
     * TestClassWithDependecy constructor.
     * @param TestClass $testClass
     */
    public function __construct(TestClass $testClass)
    {
        $this->testClass = $testClass;
    }

    /**
     * @return TestClass
     */
    public function getTestClass(): TestClass
    {
        return $this->testClass;
    }
}
<?php

declare(strict_types=1);

namespace flotzilla\Container\ContainerInstance;

use flotzilla\Container\Exceptions\ClassIsNotInstantiableException;
use ReflectionClass;

/**
 * Class ClassInstance for handling class container type
 *
 * @package flotzilla\Container\ContainerInstance
 */
class ClassInstance implements ContainerInstance
{
    /**
     * @var ReflectionClass
     */
    private $reflectionClass;

    /**
     * Constructor parameters
     *
     * @var array
     */
    private $parameters = [];

    /**
     * Check if class is not abstract
     *
     * @var bool
     */
    private $hasConstructor = false;

    /**
     * CallableClass constructor.
     *
     * @param  string $className
     * @param  array  $parameters
     * @throws \ReflectionException
     * @throws ClassIsNotInstantiableException
     */
    public function __construct(string $className, array $parameters = [])
    {
        $this->parameters = $parameters;

        $this->reflectionClass = new ReflectionClass($className);

        if (!$this->reflectionClass->isInstantiable()) {
            throw new ClassIsNotInstantiableException($className);
        }

        $this->hasConstructor = !is_null($this->reflectionClass->getConstructor());
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return ContainerInstance::TYPE_CLASS;
    }

    /**
     * Call class constructor with predefined parameters if it possible
     * or call default constructor
     *
     * @return mixed|object
     * @throws ClassIsNotInstantiableException
     */
    public function call()
    {
        if (!$this->hasConstructor && $this->parameters) {
            throw new ClassIsNotInstantiableException(
                "Class {$this->reflectionClass->getName()} cannot be called with arguments"
            );
        }

        if ($this->hasConstructor && $this->parameters) {
            return $this->reflectionClass->newInstanceArgs($this->parameters);
        }

        return $this->reflectionClass->newInstance();
    }

    /**
     * Call class constructor with parameters
     *
     * @param array $parameters constructor arguments
     *
     * @return mixed|object
     * @throws ClassIsNotInstantiableException
     */
    public function callWithParameters(array $parameters = [])
    {
        if (!$this->hasConstructor) {
            throw new ClassIsNotInstantiableException(
                "Class {$this->reflectionClass->getName()} cannot be called with arguments"
            );
        }

        if ($this->hasConstructor && $parameters) {
            return $this->reflectionClass->newInstanceArgs($parameters);
        }

        return $this->call();
    }

    /**
     * @inheritDoc
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @inheritDoc
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @return ReflectionClass
     */
    public function getReflectionClass(): ReflectionClass
    {
        return $this->reflectionClass;
    }

    /**
     * @return bool
     */
    public function hasConstructor(): bool
    {
        return $this->hasConstructor;
    }
}

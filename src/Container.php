<?php

declare(strict_types=1);

namespace flotzilla\Container;

use Closure;
use flotzilla\Container\ContainerInstance\ClassInstance;
use flotzilla\Container\ContainerInstance\ClosureInstance;
use flotzilla\Container\ContainerInstance\ContainerInstance;
use flotzilla\Container\Exceptions\ContainerNotFoundException;
use flotzilla\Container\Exceptions\ContainerServiceInitializationException;

/**
 * PSR-11 compliant container implementation
 * @package flotzilla\Container
 */
class Container implements ContainerInterface, \Countable
{
    /**
     * @var array
     */
    private $services = [];

    /**
     * @var ContainerInstance[]
     */
    private $containerFactories = [];

    /**
     * Container constructor.
     *
     * @param array $containerServices
     * @throws Exceptions\ClassIsNotInstantiableException
     * @throws \ReflectionException
     */
    public function __construct(array $containerServices = [])
    {
        foreach ($containerServices as $id => $serviceFactory) {
            $this->set($id, $serviceFactory);
        }
    }

    /**
     * @inheritdoc
     * @throws \ReflectionException
     * @throws Exceptions\ClassIsNotInstantiableException
     */
    public function set(string $id, $serviceParameters, bool $rewrite = false): void
    {
        if (array_key_exists($id, $this->containerFactories) && !$rewrite) {
            throw new ContainerServiceInitializationException("Service {$id} is already in container stack");
        }

        if ($serviceParameters instanceof Closure) {
            $this->containerFactories[$id] = new ClosureInstance($serviceParameters);
        } else if (is_array($serviceParameters)) {
            $this->initFromArray($id, $serviceParameters);
        } else if (is_string($serviceParameters)) {
            $this->initFromString($id, $serviceParameters);
        } else {
            throw new ContainerServiceInitializationException("Service {$id} cannot be instanced with current parameters");
        }
    }

    /**
     * @param string $id
     * @param array $parameters
     * @throws \ReflectionException
     * @throws Exceptions\ClassIsNotInstantiableException
     */
    private function initFromArray(string $id, array $parameters)
    {
        $className = reset($parameters);
        if (!is_string($className)) {
            throw new ContainerServiceInitializationException("Service {$id} {$className} parameter should be string");
        }

        $reflectionClass = $this->initFromString($id, $className);

        if (count($parameters) > 1) {
            $reflectionClass->setParameters(array_slice($parameters, 1));
        }
    }

    /**
     * @param string $id
     * @param string $className
     * @return ContainerInstance
     * @throws \ReflectionException
     * @throws Exceptions\ClassIsNotInstantiableException
     */
    private function initFromString(string $id, string $className): ContainerInstance
    {
        $this->containerFactories[$id] = new ClassInstance($className);

        return $this->containerFactories[$id];
    }

    /**
     * @inheritdoc
     */
    public function listServiceIds(): array
    {
        return array_keys($this->containerFactories);
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return count($this->containerFactories);
    }

    /**
     * @inheritdoc
     */
    public function get($id)
    {
        if (!$this->has($id)) {
            throw new ContainerNotFoundException($id);
        }

        if (array_key_exists($id, $this->services)) {
            return $this->services[$id];
        }

        $service = $this->getFromFactory($id);
        $this->services[$id] = $service;

        return $service;
    }

    /**
     * @inheritdoc
     */
    public function getWithParameters(string $id, array $parameters)
    {
        if (!$parameters){
            return $this->get($id);
        }

        if (!$this->has($id)) {
            throw new ContainerNotFoundException($id);
        }

        return $this->containerFactories[$id]->callWithParameters($parameters);
    }

    /**
     * @inheritdoc
     */
    public function has($id): bool
    {
        return isset($this->containerFactories[$id]);
    }

    /**
     * @param string $id
     * @return mixed
     */
    private function getFromFactory(string $id)
    {
        $serviceFactory = $this->containerFactories[$id];

        if ($params = $serviceFactory->getParameters()) {
            $resolvedDependencies = [];
            foreach ($params as $param) {
                if (is_string($param) && $this->has($param)) {
                    $resolvedDependencies[] = $this->get($param);
                } else {
                    $resolvedDependencies[] = $param;
                }
            }

            return $serviceFactory->callWithParameters($resolvedDependencies);
        }

        return $serviceFactory->call();
    }
}

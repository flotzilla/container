<?php

declare(strict_types=1);

namespace bbyte\Container;

use bbyte\Container\Exceptions\ContainerNotFoundException;
use bbyte\Container\Exceptions\ContainerServiceInitializationException;
use Closure;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface, \Countable
{
    /**
     * @var array
     */
    private $services = [];

    /**
     * @var Closure[]
     */
    private $serviceFactories = [];

    /**
     * Container constructor.
     *
     * @param array $serviceFactories
     * @throws ContainerServiceInitializationException
     */
    public function __construct(array $serviceFactories = [])
    {
        foreach ($serviceFactories as $id => $serviceFactory) {

            if (!($serviceFactory instanceof Closure)) {
                throw new ContainerServiceInitializationException($id);
            }

            $this->set($id, $serviceFactory);
        }
    }

    /**
     * @param string $id
     * @param Closure $serviceFactory
     */
    public function set(string $id, Closure $serviceFactory): void
    {
        $this->serviceFactories[$id] = $serviceFactory;

        // remove previous
        unset($this->services[$id]);
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
    public function has($id): bool
    {
        return isset($this->serviceFactories[$id]);
    }

    /**
     * @param string $id
     * @return mixed
     */
    private function getFromFactory(string $id)
    {
        $serviceFactory = $this->serviceFactories[$id];
        return $serviceFactory($this);
    }

    /**
     * Retrieve services id's
     * @return array
     */
    public function listServiceIds(): array
    {
        return array_keys($this->serviceFactories);
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return count($this->services);
    }
}

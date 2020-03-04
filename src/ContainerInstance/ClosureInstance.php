<?php

declare(strict_types=1);

namespace flotzilla\Container\ContainerInstance;

use ArgumentCountError;

/**
 * Class ClosureInstance for handling closures
 *
 * @package flotzilla\Container\ContainerInstance
 */
class ClosureInstance implements ContainerInstance
{
    /**
     * @var \Closure $closure
     */
    private $closure;

    /**
     * @var array
     */
    private $parameters = [];

    /**
     * CallableInstance constructor.
     */
    public function __construct(\Closure $closure)
    {
        $this->closure = $closure;
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return ContainerInstance::TYPE_CLOSURE;
    }

    /**
     * Call closure functon
     *
     * @return mixed
     *
     * @throws ArgumentCountError
     */
    public function call()
    {
        if ($this->parameters) {
            return $this->closure->call($this, ...$this->parameters);
        }

        return $this->closure->call($this);
    }

    /**
     * Call closure function with parameters
     *
     * @param array $parameters closure call arguments
     *
     * @return mixed
     * @throws ArgumentCountError
     */
    public function callWithParameters(array $parameters = [])
    {
        if ($parameters) {
            return $this->closure->call($this, ...$parameters);
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
}

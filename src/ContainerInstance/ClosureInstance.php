<?php

declare(strict_types=1);

namespace flotzilla\Container\ContainerInstance;

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
     * @inheritDoc
     */
    public function call()
    {
        if ($this->parameters) {
            return $this->closure->call($this, $this->parameters);
        }

        return $this->closure->call($this);
    }

    public function callWithParameters(array $parameters = [])
    {
        if ($parameters) {
            return $this->closure->call($this, ...$parameters);
        }

        return $this->call();
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
    }
}

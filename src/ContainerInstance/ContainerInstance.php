<?php

declare(strict_types=1);

namespace flotzilla\Container\ContainerInstance;

interface ContainerInstance
{
    const TYPE_CLOSURE = 'TYPE_CLOSURE';
    const TYPE_CLASS = 'TYPE_CLASS';

    /**
     * @return mixed
     */
    public function getType();

    /**
     * @return mixed
     */
    public function call();

    /**
     * @param array $parameters
     * @return mixed
     */
    public function callWithParameters(array $parameters = []);

    /**
     * @return array
     */
    public function getParameters(): array;

    /**
     * @param array $parameters
     * @return mixed
     */
    public function setParameters(array $parameters);

}
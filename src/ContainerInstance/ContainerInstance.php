<?php

declare(strict_types=1);

namespace flotzilla\Container\ContainerInstance;

interface ContainerInstance
{
    const TYPE_CLOSURE = 'TYPE_CLOSURE';
    const TYPE_CLASS = 'TYPE_CLASS';

    /**
     * Return instance container type
     *
     * @return mixed
     */
    public function getType();

    /**
     * Call Instance main executor
     *
     * @return mixed
     */
    public function call();

    /**
     * Call executor with parameters
     *
     * @param array $parameters parameters to execute with
     *
     * @return mixed
     */
    public function callWithParameters(array $parameters = []);

    /**
     * @return array
     */
    public function getParameters(): array;

    /**
     * @param array $parameters
     *
     * @return mixed
     */
    public function setParameters(array $parameters);
}

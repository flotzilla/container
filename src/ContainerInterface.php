<?php

declare(strict_types=1);

namespace flotzilla\Container;

use Psr\Container\ContainerInterface as PSRContainerInterface;

/**
 * PSR-11 container interface
 */
interface ContainerInterface extends PSRContainerInterface
{
    /**
     * Retrieve services id's
     *
     * @return array
     */
    public function listServiceIds(): array;

    /**
     * Sets a dependency
     *
     * @param string $id      Service unique id
     * @param mixed  $service Concrete service
     *
     * @return void
     */
    public function set(string $id, $service): void;

    /**
     * Get container with constructor parameters or with closure function arguments
     *
     * @param string $id         Container identifications
     * @param array  $parameters Parameters to be called with container executor
     *
     * @return mixed
     */
    public function getWithParameters(string $id, array $parameters);
}

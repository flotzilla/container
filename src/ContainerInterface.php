<?php

declare(strict_types=1);

namespace flotzilla\Container;

use Closure;
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
     * @param string  $id      Service unique id
     * @param Closure $service Concrete service
     *
     * @return void
     */
    public function set(string $id, Closure $service): void;
}

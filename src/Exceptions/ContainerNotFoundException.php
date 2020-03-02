<?php

declare(strict_types=1);

namespace flotzilla\Container\Exceptions;

use Psr\Container\NotFoundExceptionInterface;

class ContainerNotFoundException extends \InvalidArgumentException implements NotFoundExceptionInterface
{
    protected $message = "DI Service not found";

    /**
     * ContainerNotFoundException constructor.
     * @param string $message
     * @param int $code
     * @param \Exception|null $prev
     */
    public function __construct(string $message, $code = 0, \Exception $prev = null)
    {
        $message = $message ? "Container {$message} not found" : $this->message;
        parent::__construct($message, $code, $prev);
    }
}

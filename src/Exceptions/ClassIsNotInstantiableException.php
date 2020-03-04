<?php

declare(strict_types=1);

namespace flotzilla\Container\Exceptions;

use Exception;

class ClassIsNotInstantiableException extends Exception
{
    /**
     * ClassIsNotInstantiable constructor.
     *
     * @param string         $message
     * @param int            $code
     * @param Exception|null $prev
     */
    public function __construct(string $message = "", $code = 0, \Exception $prev = null)
    {
        $message = $message ?: "Class {$message} is not instantiable";
        parent::__construct($message, $code, $prev);
    }
}

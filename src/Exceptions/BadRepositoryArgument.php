<?php

namespace Whitecube\NovaMediaCleaner\Exceptions;

class BadRepositoryArgument extends \Exception
{
    /**
     * Create a new exception instance.
     *
     * @param  string  $repositoryClass
     * @param  string  $argument
     * @param  string  $value
     * @param  string  $message
     * @return void
     */
    public function __construct($repositoryClass, $argument, $value, $message)
    {
        parent::__construct('Value "' . $value . '" for argument "' . $argument . '" of "' . $repositoryClass . '" is ' . $message . '.');
    }
}

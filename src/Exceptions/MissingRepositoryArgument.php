<?php

namespace Whitecube\NovaMediaCleaner\Exceptions;

class MissingRepositoryArgument extends \Exception
{
    /**
     * Create a new exception instance.
     *
     * @param  string  $repositoryClass
     * @param  string  $argument
     * @return void
     */
    public function __construct($repositoryClass, $argument)
    {
        parent::__construct('Missing argument "' . $argument . '" for "' . $repositoryClass . '" instanciation.');
    }
}

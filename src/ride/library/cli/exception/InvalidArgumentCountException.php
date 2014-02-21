<?php

namespace ride\library\cli\exception;

/**
 * Exception thrown when a command received an invalid number of arguments
 */
class InvalidArgumentCountException extends CliException {

    /**
     * Constructs a new invalid argument count exception
     * @return null
     */
    public function __construct() {
        parent::__construct('Invalid argument count');
    }

}
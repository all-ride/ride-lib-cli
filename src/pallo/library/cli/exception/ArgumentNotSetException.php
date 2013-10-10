<?php

namespace pallo\library\cli\exception;

/**
 * Exception thrown when a command didn't receive a required argument
 */
class ArgumentNotSetException extends CliException {

    /**
     * Constructs a new argument not set exception
     * @param string $argument Name of the argument
     * @return null
     */
    public function __construct($argument) {
        parent::__construct('No ' . $argument . ' provided');
    }

}
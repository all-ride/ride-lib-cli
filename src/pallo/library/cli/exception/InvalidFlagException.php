<?php

namespace pallo\library\cli\exception;

/**
 * Exception thrown when a command received a undefined flag
 */
class InvalidFlagException extends CliException {

    /**
     * Constructs a new invalid flag exception
     * @param string $flag Name of the flag
     * @return null
     */
    public function __construct($flag) {
        parent::__construct('Flag ' . $flag . ' is not available');
    }

}
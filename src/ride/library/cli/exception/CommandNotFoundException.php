<?php

namespace ride\library\cli\exception;

/**
 * Exception thrown when a command is not found
 */
class CommandNotFoundException extends CliException {

    /**
     * Command string
     * @var string
     */
    protected $command;

    /**
     * Constructs a new command not found exception
     * @param string $command Command string
     * @return null
     */
    public function __construct($command) {
        parent::__construct('Command not found: ' . $command);

        $this->command = $command;
    }

    /**
	 * Gets the command which could not be found
	 * @return string
     */
    public function getCommand() {
        return $this->command;
    }

}
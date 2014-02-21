<?php

namespace ride\library\cli\command;

/**
 * Command to exit the interactive shell
 */
class ExitCommand extends AbstractCommand {

    /**
     * Name of this command
     * @var string
     */
    const NAME = 'exit';

    /**
     * Constructs a new exit command
     * @return null
     */
    public function __construct() {
        parent::__construct(self::NAME, 'Exit the console.');
    }

    /**
     * Executes the command
     * @return null
     */
    public function execute() {
        // dummy command, the real exit is in the cli
    }

}
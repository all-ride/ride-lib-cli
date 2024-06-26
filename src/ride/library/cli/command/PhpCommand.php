<?php

namespace ride\library\cli\command;

/**
 * Command to execute PHP code
 */
class PhpCommand extends AbstractCommand {

    /**
     * The name of this command
     * @var string
     */
    const NAME = 'php';

    /**
     * Constructs a new php command
     * @return null
     */
    public function __construct() {
        parent::__construct(self::NAME, 'Executes PHP code.');

        $this->addArgument('code', 'PHP code');
    }

    /**
     * Executes the command
     * @return null
     */
    public function execute() {
        // dummy command, the real implementation is in the cli
    }

}
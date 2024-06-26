<?php

namespace ride\library\cli\command;

class TestCommand extends AbstractCommand {

    private $isInvoked;

    public function __construct() {
        parent::__construct('name', 'description');

        $this->addAlias('n');

        $this->addArgument('required', 'required description');
        $this->addArgument('optional', 'optional description', false);
        $this->addArgument('dynamic', 'dynamic description', false, true);

        $this->addFlag('flag', 'flag description');

        $this->isInvoked = false;
    }

    public function execute() {
        $this->isInvoked = true;
    }

    public function isInvoked() {
        return $this->isInvoked;
    }

}

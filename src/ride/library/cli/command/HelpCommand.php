<?php

namespace ride\library\cli\command;

/**
 * Command to view and modify the configuration
 */
class HelpCommand extends AbstractCommand {

    /**
     * Name of this command
     * @var string
     */
    const NAME = 'help';

    /**
     * Container of commands
     * @var CommandContainer
     */
    protected $commandContainer;

    /**
     * Constructs a new config command
     * @return null
     */
    public function __construct(CommandContainer $commandContainer) {
        $this->commandContainer = $commandContainer;

        parent::__construct(self::NAME, 'Prints this help.');

		$this->addArgument('command', 'Provide a name of a command to get the detailed help of the command', false, true);
    }

    /**
     * Executes the command
     * @return null
     */
    public function execute() {
    	$command = $this->input->getArgument('command');
    	if ($command) {
    		$this->showCommand($command);
    	} else {
    		$this->showOverview();
    	}
    }

    /**
     * Writes the help of a command to the output
     * @param string $command Name of the command
     * @return null
     */
    protected function showCommand($command) {
    	$command = $this->commandContainer->getCommand($command);

    	$description = $command->getDescription();
    	$arguments = $command->getArguments();
    	$flags= $command->getFlags();

    	if ($description) {
    		$this->output->writeLine($description);
			$this->output->writeLine('');
    	}

    	$this->output->writeLine('Syntax: ' . $command->getSyntax());
    	foreach ($flags as $flag => $description) {
    	    $this->output->writeLine('- [--' . $flag . '] ' . $description);
    	}
		foreach ($arguments as $argument) {
			$this->output->writeLine('- ' . $argument);
		}
        $this->output->writeLine('');
    }

    /**
     * Writes an overview of all the commands to the output
     * @return null
     */
    protected function showOverview() {
        $this->output->writeLine('Available commands:');

        foreach ($this->commandContainer as $command) {
            $this->output->writeLine('- ' . $command->getSyntax());
        }

        $this->output->writeLine('');
        $this->output->writeLine('Use \'help <command>\' to get help for a specific command.');
        $this->output->writeLine('');
    }

}
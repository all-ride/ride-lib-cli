<?php

namespace ride\library\cli\command;

use ride\library\cli\input\AutoCompletable;

/**
 * Command to view and modify the configuration
 */
class HelpCommand extends AbstractCommand implements AutoCompletable {

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
        $command = $this->commandContainer->replaceAliases($command);
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

        $aliases = $command->getAliases();
        if ($aliases) {
            $this->output->writeLine('');
            $this->output->writeLine('Alias: ' . implode(',', $aliases));
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
            $syntax = $command->getSyntax();

            $aliases = $command->getAliases();
            if ($aliases) {
                $syntax .= ' (' . implode(',', $aliases) . ')';
            }

            $this->output->writeLine('- ' . $syntax);
        }

        $this->output->writeLine('');
        $this->output->writeLine('Use \'help <command>\' to get help for a specific command.');
        $this->output->writeLine('');
    }

    /**
     * Performs auto complete on the provided input
     * @param string $input Input value to auto complete
     * @return array Array with the auto completion matches
     */
    public function autoComplete($input) {
        $completion = array();

        foreach ($this->commandContainer as $command) {
            if (!$input || strpos($command->getName(), $input) === 0) {
                $completion[] = $command->getName();
            }

            foreach ($command->getAliases() as $alias) {
                if (!$input || strpos($alias, $input) === 0) {
                    $completion[] = $alias;
                }
            }
        }

        return $completion;
    }

}

<?php

namespace ride\library\cli;

use ride\library\cli\command\CommandContainer;
use ride\library\cli\exception\ArgumentNotSetException;
use ride\library\cli\exception\CommandNotFoundException;
use ride\library\cli\exception\FlagNotFoundException;
use ride\library\cli\exception\InvalidArgumentCountException;
use ride\library\cli\input\ArgumentParser;
use ride\library\cli\input\Input;
use ride\library\cli\output\Output;

/**
 * Interpreter for the CLI commands
 */
class CommandInterpreter {

    /**
     * Instance of the command container
     * @var ride\library\cli\command\CommandContainer
     */
    protected $commandContainer;

    /**
     * Instance of the argument parser
     * @var ride\library\cli\input\ArgumentParser
     */
    protected $argumentParser;

    /**
     * Constructs a new command interpreter
     * @param ride\library\cli\command\CommandContainer $commandContainer
     * @param ride\library\cli\input\ArgumentParser $argumentParser
     * @return null
     */
    public function __construct(CommandContainer $commandContainer, ArgumentParser $argumentParser = null) {
        if (!$argumentParser) {
            $argumentParser = new ArgumentParser();
        }

        $this->commandContainer = $commandContainer;
        $this->argumentParser = $argumentParser;
    }

    /**
     * Gets the command container
     * @return ride\library\cli\command\CommandContainer
     */
    public function getCommandContainer() {
        return $this->commandContainer;
    }

    /**
     * Interprets the provided command
     * @param string $command Command input to interpret
     * @param ride\library\cli\input\Input $input Input implementation
     * @param ride\library\cli\output\Output $output Output implementation
     * @return null
     * @throws ride\library\cli\exception\CliException when the command does
     * not exist
     */
    public function interpret($command, Input $input, Output $output) {
        // find the command
        $runCommand = null;
        foreach ($this->commandContainer as $commandName => $commandInstance) {
            $commandNameLength = strlen($commandName);
            if (strncmp($command, $commandName, $commandNameLength) != 0) {
                continue;
            }

            if (!$runCommand || strlen($runCommand->getName()) < $commandNameLength) {
                $runCommand = $commandInstance;
            }
        }

        if (!$runCommand) {
            throw new CommandNotFoundException($command);
        }

        // parse the command
        $commandInput = $this->argumentParser->getCommandInput($command, substr_count($runCommand->getName(), ' '));

        // validate arguments
        $index = 0;
        $arguments = $runCommand->getArguments();
        foreach ($arguments as $argument) {
            if ($argument->isRequired()) {
                $value = $commandInput->getArgument($index);
                if ($value === null || $value === '') {
                    throw new ArgumentNotSetException($argument->getName());
                }
            }

            if ($commandInput->hasArgument($index)) {
                if ($argument->isDynamic()) {
                    $commandInput->nameDynamicArgument($index, $argument->getName());
                } else {
                    $commandInput->nameArgument($index, $argument->getName());
                }
            }

            $index++;
        }

        if (count($arguments) < $commandInput->getArgumentCount()) {
            throw new InvalidArgumentCountException();
        }

        // validate flags
        $flags = $runCommand->getFlags();
        $inputFlags = $commandInput->getFlags();
        foreach ($inputFlags as $flag => $value) {
            if (!isset($flags[$flag])) {
                throw new InvalidFlagException($flag);
            }
        }

        // execute the command
        $commandInput->setInput($input);

        $runCommand->setCommandInput($commandInput);
        $runCommand->setOutput($output);

        $runCommand->execute();
    }

}
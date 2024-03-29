<?php

namespace ride\library\cli\command;

use ride\library\cli\exception\CliException;
use ride\library\cli\input\AutoCompletable;

use \Countable;
use \Iterator;

/**
 * Container of commands
 */
class CommandContainer implements AutoCompletable, Countable, Iterator {

    /**
     * Commands in this container
     * @var array
     */
    protected $commands = array();

    /**
     * Aliases of the commands
     * @var array
     */
    protected $aliases = array();

    /**
     * Adds a command in this container
     * @param \ride\library\cli\command\Command $command Command to add
     * @return null
     * @throws \ride\library\cli\exception\CliException when a alias of the
     * command is already used
     */
    public function addCommand(Command $command) {
        $commandName = $command->getName();

        $this->removeCommand($commandName);

        $this->commands[$commandName] = $command;

        ksort($this->commands);

        $aliases = $command->getAliases();
        foreach ($aliases as $alias) {
            if (isset($this->aliases[$alias])) {
                throw new CliException('Could not add command: alias ' . $alias . ' is already used by ' . $this->aliases[$alias]);
            }

            $this->aliases[$alias] = $commandName;
        }
    }

    /**
     * Removes a command from this container
     * @param string $name Name of the command
     * @return boolean True when the command has been removed, false otherwise
     */
    public function removeCommand($name) {
        if (!$this->hasCommand($name)) {
            return false;
        }

        unset($this->commands[$name]);

        foreach ($this->aliases as $alias => $commandName) {
            if ($commandName == $name) {
                unset($this->aliases[$alias]);
            }
        }

        return true;
    }

    /**
     * Checks if a command is registered
     * @param string $name
     * @return boolean True when the command is registered, false otherwise
     * @throws \ride\library\cli\exception\CliException when the provided name
     * is empty or invalid
     */
    public function hasCommand($name) {
        if (!is_string($name) || !$name) {
            throw new CliException('Could not check availability of command: provided name is invalid or empty');
        }

        return isset($this->commands[$name]);
    }

    /**
     * Gets a command by its name
     * @param string $name
     * @return Command
     * @throws \ride\library\cli\exception\CliException when the command if not
     * in this container
     */
    public function getCommand($name) {
        if (!$this->hasCommand($name)) {
            throw new CliException('Could not get command ' . $name . ': command not added to the container');
        }

        return $this->commands[$name];
    }

    /**
     * Gets all the commands
     * @return array Array with the name of the command as key and an instance
     * of Command as value
     */
    public function getCommands() {
        return $this->commands;
    }

    /**
     * Replaces the aliases with their full command
     * @param string $input Input value to replace
     * @return string Input value with aliases replaced
     */
    public function replaceAliases($input) {
        foreach ($this->aliases as $alias => $commandName) {
            $aliasLength = strlen($alias);
            if ($input === $alias || strncmp($input, $alias . ' ', $aliasLength + 1) === 0) {
                $input = $commandName . substr($input, $aliasLength);

                break;
            }
        }

        return $input;
    }

    /**
     * Performs auto complete on the provided input
     * @param string $input The input value to auto complete
     * @return array|null Array with the auto completion matches or null when
     * no auto completion is available
     */
    public function autoComplete($input) {
        $input = $this->replaceAliases($input);

        $commands = array();
        foreach ($this->commands as $commandName => $command) {
            $commandNameLength = strlen($commandName);
            $inputLength = strlen($input);
            if (strncmp($commandName, $input, $inputLength) === 0 || strncmp($input, $commandName, $commandNameLength) === 0) {
                $commands[$commandName] = $command;
            }
        }

        $tokens = explode(' ', $input);
        $numTokens = count($tokens);

        $completion = array();
        foreach ($commands as $commandName => $command) {
            $commandTokens = explode(' ', $commandName);
            $numCommandTokens = count($commandTokens);

            if ($numTokens < $numCommandTokens) {
                $commandName = '';
                for ($i = 0; $i < $numTokens; $i++) {
                    $commandName .= ($commandName ? ' ' : '') . $commandTokens[$i];
                }

                $completion[$commandName] = $commandName;
            } elseif ($numTokens == $numCommandTokens) {
                $completion[$commandName] = $commandName;
            } elseif ($command instanceof AutoCompletable) {
                $commandInput = substr($input, strlen($commandName) + 1);
                $commandCompletion = $command->autoComplete($commandInput);

                foreach ($commandCompletion as $commandAutoComplete) {
                    $completion[$commandName . ' ' . $commandAutoComplete] = $commandName . ' ' . $commandAutoComplete;
                }
            }
        }

        return $completion;
    }

    /**
     * Implementation of the rewind() method of the {@link Iterator Iterator interface}
     * @return null
     */
    #[\ReturnTypeWillChange]
    public function rewind() {
        reset($this->commands);
    }

    /**
     * Implementation of the current() method of the {@link Iterator Iterator interface}
     * @return Message a message
     */
    #[\ReturnTypeWillChange]
    public function current() {
        return current($this->commands);
    }

    /**
     * Implementation of the key() method of the {@link Iterator Iterator interface}
     * @return int the pointer of the current message
     */
    #[\ReturnTypeWillChange]
    public function key() {
        return key($this->commands);
    }

    /**
     * Implementation of the next() method of the {@link Iterator Iterator interface}
     * @return null
     */
    #[\ReturnTypeWillChange]
    public function next() {
        return next($this->commands);
    }

    /**
     * Implementation of the valid() method of the {@link Iterator Iterator interface}
     * @return true if the current pointer is valid, false otherwise
     */
    #[\ReturnTypeWillChange]
    public function valid() {
        return isset($this->commands[key($this->commands)]);
    }

    /**
     * Implementation of the count() method of the {@link Countable Countable interface}
     * @return int number of messages in this container
     */
    #[\ReturnTypeWillChange]
    public function count() {
        return count($this->commands);
    }

}

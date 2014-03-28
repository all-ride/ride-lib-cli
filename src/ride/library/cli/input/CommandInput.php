<?php

namespace ride\library\cli\input;

use ride\library\cli\exception\CliException;
use ride\library\cli\output\Output;

/**
 * Data container for a parsed command input
 */
class CommandInput {

    /**
     * Full input
     * @var string
     */
    protected $fullCommand;

    /**
     * Command of input
     * @var string
     */
    protected $command;

    /**
     * Arguments which are values on itself (numeric)
     * @var array
     */
    protected $arguments;

    /**
     * Flags and named arguments
     * @var array
     */
    protected $flags;

    /**
     * The full input
     * @var string
     */
    protected $input;

    /**
     * Constructs a new input value
     * @param string $input Full command string
     * @param integer $argumentOffset Number of arguments which are actually
     * part of the command
     * @return null
     */
    public function __construct($fullCommand, $command = null, array $arguments = array(), array $flags = array()) {
        if ($command === null) {
            $command = $fullCommand;
        }

        $this->fullCommand = $fullCommand;
        $this->command = $command;
        $this->arguments = $arguments;
        $this->flags = $flags;

        $this->input = null;
    }

    /**
     * Gets the unparsed input value
     * @return string
     */
    public function getFullCommand() {
        return $this->fullCommand;
    }

    /**
     * Gets the command of the input, the first token
     * @return string
     */
    public function getCommand() {
        return $this->command;
    }

    /**
     * Checks if a argument is set
     * @return boolean True if set, false otherwise
     */
    public function hasArguments() {
        return $this->arguments ? true : false;
    }

    /**
     * Checks if a argument is set
     * @param mixed $index Index of the argument
     * @return boolean True if set, false otherwise
     */
    public function hasArgument($index) {
        return isset($this->arguments[$index]);
    }

    /**
     * Gets a argument by its index
     * @param mixed $index Index of the argument
     * @param mixed $default Default value for when the argument is not set
     * @return mixed The value of the argument if set, the default value otherwise
     */
    public function getArgument($index, $default = null) {
        if (!isset($this->arguments[$index])) {
            return $default;
        }

        return $this->arguments[$index];
    }

    /**
     * Gets the arguments of this input
     * @return array Array with numeric keys
     */
    public function getArguments() {
        return $this->arguments;
    }

    /**
     * Gets the number of arguments
     * @return integer Number of arguments
     */
    public function getArgumentCount() {
        return count($this->arguments);
    }

    /**
     * Gives a indexed argument a name
     * @param mixed $index The current index of the argument
     * @param string $name The name of the argument
     * @return null
     * @throws \ride\library\cli\exception\CliException when no argument is
     * set with the provided index
     */
    public function nameArgument($index, $name) {
        if (!isset($this->arguments[$index])) {
            throw new CliException('No argument set at index ' . $index);
        }

        $this->arguments[$name] = $this->arguments[$index];

        unset($this->arguments[$index]);
    }

    /**
     * Gives a indexed argument a name and concats all the following arguments
     * to it
     * @param mixed $index The current index of the argument
     * @param string $name The name of the argument
     * @return null
     * @throws \ride\library\cli\exception\CliException when no argument is
     * set with the provided index
     */
    public function nameDynamicArgument($index, $name) {
        $this->nameArgument($index, $name);

        $index++;

        $numArguments = $this->getArgumentCount();
        for ($i = $index; $i < $numArguments; $i++) {
            $this->arguments[$name] .= ' ' . $this->arguments[$i];

            unset($this->arguments[$i]);
        }
    }

    /**
     * Checks if a flag is set
     * @return boolean True if set, false otherwise
     */
    public function hasFlags() {
        return $this->flags ? true : false;
    }

    /**
     * Checks if a flag is set
     * @param string $name The name of the flag
     * @return boolean True if set, false otherwise
     */
    public function hasFlag($name) {
        return isset($this->flags[$name]);
    }

    /**
     * Gets a flag by its name
     * @param string $name The name of the flag
     * @param mixed $default The default value for when the flag is not set
     * @return mixed The value of the flag if set, the default value otherwise
     */
    public function getFlag($name, $default = null) {
        if (!isset($this->flags[$name])) {
            return $default;
        }

        return $this->flags[$name];
    }

    /**
     * Gets the flags of this input
     * @return array Array with named keys
     */
    public function getFlags() {
        return $this->flags;
    }

    /**
     * Sets the input implementation
     * @param \ride\library\cli\input\Input $input
     * @return null
     */
    public function setInput(Input $input) {
        $this->input = $input;
    }

    /**
     * Checks if this input is interactive
     * @return boolean
     */
    public function isInteractive() {
        if (!$this->input) {
            throw new CliException('Could not check if input is interactive: no input set to wrap around');
        }

        return $this->input->isInteractive();
    }

    /**
     * Reads a line from the input
     * @param \ride\library\cli\output\Output $output
     * @param string $prompt Prompt for the input
     * @return string Input value
     * @throws \ride\library\cli\exception\CliException when no input set to
     * wrap around
     */
    public function read(Output $output, $prompt) {
        if (!$this->input) {
            throw new CliException('Could not read from input: no input set to wrap around');
        }

        return $this->input->read($output, $prompt);
    }

}
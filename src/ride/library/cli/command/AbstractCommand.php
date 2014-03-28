<?php

namespace ride\library\cli\command;

use ride\library\cli\exception\CliException;
use ride\library\cli\input\CommandInput;
use ride\library\cli\output\Output;

/**
 * Abstract implementation of a command
 */
abstract class AbstractCommand implements Command {

    /**
     * Name of this command
     * @var string
     */
	protected $name;

	/**
	 * Short description of this command
	 * @var string
	 */
	protected $description;

	/**
	 * Definitions of the arguments
	 * @var array
	 */
	protected $arguments;

	/**
	 * Definitions of the flags
	 * @var array
	 */
	protected $flags;

	/**
	 * Command input
	 * @var \ride\library\cli\input\CommandInput
	 */
    protected $input;

	/**
	 * Output implementation
	 * @var \ride\library\cli\output\Output
	 */
	protected $output;

	/**
	 * Constructs a new command
	 * @param string $name The command
	 * @param string $description A short description of the command
	 * @param string $syntax The syntax of this command
	 * @return null
	 */
	public function __construct($name, $description = null) {
		$this->setName($name);
		$this->setDescription($description);

		$this->arguments = array();
		$this->flags = array();
	}

	/**
	 * Sets the name of this command
	 * @param string $name
	 * @return null
	 * @throws Exception when the name is invalid
	 */
	protected function setName($name) {
		if (!is_string($name) || !$name) {
			throw new CliException('Provided name is empty or invalid');
		}

		$this->name = $name;
	}

	/**
	 * Gets the name of this command
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Sets the short description of this command
	 * @param string $description
	 * @return null
	 * @throws Exception when the description is invalid
	 */
	protected function setDescription($description) {
		if ($description !== null && (!is_string($description) || !$description)) {
			throw new CliException('Provided description is empty or invalid');
		}

		$this->description = $description;
	}

	/**
	 * Gets a short description of this command
	 * @return string|null
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * Gets the syntax of this command
	 * @return string|null
	 */
	public function getSyntax() {
		$optionalArguments = 0;

		$syntax = $this->name;

		foreach ($this->flags as $flag => $description) {
		    $syntax .= ' [--' . $flag . ']';
		}

		foreach ($this->arguments as $argument) {
			if ($argument->isRequired()) {
				$syntax .= ' <' . $argument->getName() . '>';

				continue;
			}

			$syntax .= ' [<' . $argument->getName() . '>';

			$optionalArguments++;
		}

		$syntax .= str_repeat(']', $optionalArguments);

		return $syntax;
	}

	/**
	 * Adds the definition of a argument for this command. Arguments should be
	 * added in the order of the syntax, optional arguments as last
	 * @param string name Name of the argument
	 * @param string description Description of the argument
	 * @param boolean $isRequired Flag to see if the argument is required
	 * @param boolean $isDynamic Flag to see if the argument is dynamic
	 * @return null
	 * @throws \ride\library\cli\exception\CliException when the previous
	 * argument is optional and this one is not
	 */
	protected function addArgument($name, $description, $isRequired = true, $isDynamic = false) {
		$argument = new CommandArgument($name, $description, $isRequired, $isDynamic);

		if ($this->arguments) {
			list($lastArgument) = array_slice($this->arguments, -1);

			if ($lastArgument->isDynamic()) {
				throw new CliException("Cannot add a argument after a dynamic argument");
			}

			if (!$lastArgument->isRequired() && $argument->isRequired()) {
				throw new CliException("Cannot add a required argument after a optional argument");
			}
		}

		$this->arguments[] = $argument;
	}

	/**
	 * Gets the definitions of the arguments
	 * @return array
	 * @see CommandArgument
	 */
	public function getArguments() {
		return $this->arguments;
	}

	/**
	 * Adds the definition of a flag for this command
	 * @param string name Name of the argument
	 * @param string description Description of the argument
	 * @return null
	 */
	protected function addFlag($name, $description) {
		$this->flags[$name] = $description;
	}

	/**
	 * Gets the description of the flags
	 * @return array Array with the name of the flag as key and the description
	 * as value
	 */
	public function getFlags() {
	    return $this->flags;
	}

	/**
	 * Sets the command input
	 * @param \ride\library\cli\CommandInput $input
	 * @return null
	 */
	public function setCommandInput(CommandInput $input) {
	    $this->input = $input;
	}

	/**
	 * Sets the output implementation
	 * @param \ride\library\cli\output\Output $output
	 * @return null
	*/
	public function setOutput(Output $output) {
	    $this->output = $output;
	}

}
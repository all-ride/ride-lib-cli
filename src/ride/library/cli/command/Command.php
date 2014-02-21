<?php

namespace ride\library\cli\command;

use ride\library\cli\input\CommandInput;
use ride\library\cli\output\Output;

/**
 * Interface for a console command
 */
interface Command {

    /**
     * Gets the name of the command
     * @return string
     */
	public function getName();

	/**
	 * Gets a short description of the command
	 * @return string
	 */
	public function getDescription();

	/**
	 * Gets the syntax of the command
	 * @return string
	 */
	public function getSyntax();

	/**
	 * Gets the description of the flags
	 * @return array Array with the name of the flag as key and the description
	 * as value
	 */
	public function getFlags();

	/**
	 * Gets the definitions of the arguments
	 * @return array Array of Argument instances
	 * @see ride\library\cli\command\CommandArgument
	 */
	public function getArguments();

	/**
	 * Sets the command input
	 * @param ride\library\cli\input\CommandInput $input
	 * @return null
	 */
	public function setCommandInput(CommandInput $input);

	/**
	 * Sets the output implementation
	 * @param ride\library\cli\output\Output $output
	 */
	public function setOutput(Output $output);

	/**
	 * Execute the command
	 * @return null
	 */
	public function execute();

}
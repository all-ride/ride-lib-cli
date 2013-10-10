<?php

namespace pallo\library\cli\command;

use pallo\library\cli\input\CommandInput;
use pallo\library\cli\output\Output;

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
	 * @see pallo\library\cli\command\CommandArgument
	 */
	public function getArguments();

	/**
	 * Sets the command input
	 * @param pallo\library\cli\input\CommandInput $input
	 * @return null
	 */
	public function setCommandInput(CommandInput $input);

	/**
	 * Sets the output implementation
	 * @param pallo\library\cli\output\Output $output
	 */
	public function setOutput(Output $output);

	/**
	 * Execute the command
	 * @return null
	 */
	public function execute();

}
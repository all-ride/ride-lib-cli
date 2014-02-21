<?php

namespace ride\library\cli\output;

/**
 * Interface for the output
 */
interface Output {

	/**
	 * Writes some output to standard out
	 * @param string $output
	 * @return null
	 */
	public function write($output);

	/**
	 * Writes a line to standard out
	 * @param string $output
	 * @return null
	 */
	public function writeLine($output);

	/**
	 * Writes some output to the error out
	 * @param string $output
	 * @return null
	 */
	public function writeError($output);

	/**
	 * Writes a line to the error out
	 * @param string $output
	 * @return null
	 */
	public function writeErrorLine($output);

}
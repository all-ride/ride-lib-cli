<?php

namespace ride\library\cli\output;

use ride\library\cli\exception\CliException;

/**
 * Stream implementation of the output interface
 */
class ArrayOutput implements Output {

    /**
     * Array with all output messages
     * @var array
     */
    private $out = array();

    /**
     * Current output line
     * @var string
     */
    private $currentOut = null;

    /**
     * Array with all error messages
     * @var array
     */
    private $err = array();

    /**
     * Current error line
     * @var string
     */
    private $currentErr = null;

	/**
	 * Writes some output to standard out
	 * @param string $output
	 * @return null
	 */
	public function write($output) {
        $this->currentOut .= $output;
	}

	/**
	 * Writes a line to standard out
	 * @param string $output
	 * @return null
	 */
	public function writeLine($output) {
        $this->write($output);

        $this->out[] = $this->currentOut;

        $this->currentOut = null;
	}

    /**
     * Gets the standard output
     * @return array
     */
    public function getOutput() {
        $out = $this->out;

        if ($this->currentOut) {
            $out[] = $this->currentOut;
        }

        return $out;
    }

	/**
	 * Writes some output to the error out
	 * @param string $output
	 * @return null
	 */
	public function writeError($output) {
        $this->currentErr .= $output;
	}

	/**
	 * Writes a line to the error out
	 * @param string $output
	 * @return null
	 */
	public function writeErrorLine($output) {
        $this->writeError($output);

        $this->err[] = $this->currentErr;

        $this->currentErr = null;
	}

    /**
     * Gets the error output
     * @return array
     */
    public function getErrorOutput() {
        $out = $this->err;

        if ($this->currentErr) {
            $out[] = $this->currentErr;
        }

        return $out;
    }

}

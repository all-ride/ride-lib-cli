<?php

namespace pallo\library\cli\output;

use pallo\library\cli\exception\CliException;

/**
 * Stream implementation of the output interface
 */
class StreamOutput implements Output {

    /**
     * Handle of the output stream
     * @var resource
     */
    protected $out;

    /**
     * Handle of the error stream
     * @var resource
     */
    protected $err;

    /**
     * Constructs a new stream output
     * @param resource $out Handle of the output stream
     * @param resource $err Handle of the error stream
     * @throws CliException when an invalid handle is provided
     */
    public function __construct($out, $err = null) {
        if ($err === null) {
            $this->err = $out;
        } elseif (!is_resource($err)) {
            throw new CliException('Could not create stream output: error handle is not a resource');
        } else {
            $this->err = $err;
        }

        if (!is_resource($out)) {
            throw new CliException('Could not create stream output: output handle is not a resource');
        } else {
            $this->out = $out;
        }
    }

	/**
	 * Writes some output to standard out
	 * @param string $output
	 * @return null
	 */
	public function write($output) {
	    fwrite($this->out, $output);
	}

	/**
	 * Writes a line to standard out
	 * @param string $output
	 * @return null
	 */
	public function writeLine($output) {
	    $this->write($output . "\n");
	}

	/**
	 * Writes some output to the error out
	 * @param string $output
	 * @return null
	 */
	public function writeError($output) {
	    fwrite($this->err, $output);
	}

	/**
	 * Writes a line to the error out
	 * @param string $output
	 * @return null
	 */
	public function writeErrorLine($output) {
	    $this->writeError($output . "\n");
	}

}
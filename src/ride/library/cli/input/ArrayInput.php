<?php

namespace ride\library\cli\input;

use ride\library\cli\output\Output;

/**
 * Implementation of input based on an array of commands
 */
class ArrayInput implements Input {

    /**
     * Constructs a new input
     * @param array $commands Commands to iterate from with each read call
     */
    public function __construct(array $commands) {
        $this->commands = $commands;

        reset($this->commands);
    }

    /**
     * Checks if this input is interactive
     * @return boolean
     */
    public function isInteractive() {
        return false;
    }

    /**
     * Reads a line from the input
     * @param \ride\library\cli\output\Output $output
     * @param string $prompt Prompt for the input
     * @return string Input value
     */
    public function read(Output $output, $prompt) {
        $input = each($this->commands);
        if ($input === false) {
            return null;
        }

        return $input['value'];
    }

}

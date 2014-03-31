<?php

namespace ride\library\cli\input;

use ride\library\cli\output\Output;

/**
 * PHP implementation for input in a CLI environment
 */
class PhpInput implements Input {

    /**
     * Checks if this input is interactive
     * @return boolean
     */
    public function isInteractive() {
        return true;
    }

    /**
     * Reads a line from the input
     * @param \ride\library\cli\output\Output $output
     * @param string $prompt Prompt for the input
     * @return string Input value
     */
    public function read(Output $output, $prompt) {
        $output->write($prompt);

        return trim(fgets(STDIN));
    }

}
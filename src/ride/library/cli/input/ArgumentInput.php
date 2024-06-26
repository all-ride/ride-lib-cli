<?php

namespace ride\library\cli\input;

use ride\library\cli\command\HelpCommand;
use ride\library\cli\output\Output;

/**
 * Implementation of input to take 1 command direct from the command line
 */
class ArgumentInput implements Input {
    protected $input;

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
        if (isset($this->input)) {
            return null;
        }

        if (isset($_SERVER['argv']) && $_SERVER['argv']) {
            $arguments = $_SERVER['argv'];

            // escape quotes in arguments with spaces
            foreach ($arguments as $index => $argument) {
                if (strpos($argument, ' ') === false) {
                    continue;
                }

                $arguments[$index] = '"' . addslashes($argument) . '"';
            }

            $this->input = implode(' ', $arguments);
        } else {
            $this->input = HelpCommand::NAME;
        }

        return $this->input;
    }

}

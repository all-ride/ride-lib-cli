<?php

namespace pallo\library\cli\input;

use pallo\library\cli\output\Output;

/**
 * Interface for the input in a CLI environment
 */
interface Input {

    /**
     * Checks if this input is interactive
     * @return boolean
     */
    public function isInteractive();

    /**
     * Reads a line from the input
     * @param pallo\library\cli\output\Output $output
     * @param string $prompt Prompt for the input
     * @return string Input value
     */
    public function read(Output $output, $prompt);

}
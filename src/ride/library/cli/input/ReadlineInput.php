<?php

namespace ride\library\cli\input;

use ride\library\cli\exception\CliException;
use ride\library\cli\output\Output;

/**
 * Readline implementation for a interactive shell input in a CLI environment
 */
class ReadlineInput implements AutoCompletableInput {

    /**
     * Added auto completions
     * @var array
     */
    protected $autoCompletions;

    /**
     * Constructs a new Readline input
     * @throws Exception when the Readline PHP extension is not available
     */
    public function __construct() {
        if (!function_exists('readline')) {
            throw new CliException('The Readline PHP extension is not installed or not enabled. Check your PHP installation.');
        }

        $this->autoCompletions = array();
    }

    /**
     * Adds a auto completion implementation to this input
     * @param \ride\library\cli\input\AutoCompletable $autoCompletable
     * @return null
     */
    public function addAutoCompletion(AutoCompletable $autoCompletable) {
        if (!$this->autoCompletions) {
            readline_completion_function(array($this, 'performAutoComplete'));
        }

        $this->autoCompletions[] = $autoCompletable;
    }

    /**
     * Performs auto complete on the provided input
     * @param string $input The input value
     * @return array|null Array with the auto completion matches or null when
     * no auto completion is available
     */
    public function performAutoComplete($string, $position) {
        // get the full input
        $info = readline_info();
        $input = substr($info['line_buffer'], 0, $info['end']);

        if ($info['point'] !== $info['end']) {
            return true;
        }

        // get all the matches
        $matches = array();
        foreach ($this->autoCompletions as $autoCompletable) {
            $matches = $autoCompletable->autoComplete($input);
            if ($matches) {
                break;
            }
        }

        // process the matches, remove the input from the matches
        foreach ($matches as $matchIndex => $match) {
			$matches[$matchIndex] = substr($match, $position);

            if ($matches[$matchIndex] == '' || $input == $match) {
                unset($matches[$matchIndex]);

                continue;
            }
        }

        // return the result
        if (!$matches) {
            return null;
        }

        return $matches;
    }

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
        $input = readline($prompt);

        if (!empty($input)) {
            readline_add_history($input);
        }

        return $input;
    }

}

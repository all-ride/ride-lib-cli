<?php

namespace pallo\library\cli\input;

use pallo\library\cli\exception\CliException;

/**
 * Command line argument parser
 */
class ArgumentParser {

    /**
     * Gets a CommandInput from a input string
     * @param string $command Command input string
     * @param integer $argumentOffset Number of arguments which are actually
     * part of the command
     * @return CommandInput
     */
    public function getCommandInput($command, $argumentOffset = 0) {
        $arguments = array();
        $flags = array();

        $position = strpos($command, ' ');
        if ($position === false) {
            return new CommandInput($command);
        }

        $fullCommand = $command;

        $command = substr($command, 0, $position);

        $arguments = substr($fullCommand, $position);
        $arguments = $this->getArguments($arguments);
        $parsedArguments = $this->parseArguments($arguments);

        $arguments = array();
        $flags = array();

        foreach ($parsedArguments as $key => $value) {
            if (is_numeric($key)) {
                if ($argumentOffset) {
                    $command .= ' ' . $value;

                    $argumentOffset--;
                } else {
                    $arguments[] = $value;
                }
            } else {
                $flags[$key] = $value;
            }
        }

        return new CommandInput($fullCommand, $command, $arguments, $flags);
    }

    /**
     * Gets an array of the argument string
     * @param string $string Argument string
     * @return array Array with the arguments
     * @throws Exception when the provided string is invalid
     */
    public function getArguments($string) {
        if (!is_string($string)) {
            throw new CliException('Could not get the arguments: provided string is invalid (' . gettype($string) . ')');
        }

        $arguments = array();
        $argument = '';

        $open = false;

        $stringLength = strlen($string);
        for ($i = 0; $i < $stringLength; $i++) {
            $char = $string[$i];

            if ($open) {
                if ($char == $open) {
                    if ($i > 1 && $string[$i - 1] == "\\") {
                        // escape open symbol
                        $argument{strlen($argument) - 1} = $open;
                    } else {
                        // closing
                        $arguments[] = $argument;
                        $argument = '';

                        $open = null;
                    }
                } else {
                    // not the open symbol
                    $argument .= $char;
                }

                continue;
            }

            if ($char == '"' || $char == "'") {
                // opening a string
                $open = $char;

                continue;
            }

            if ($char == ' ') {
                // next argument
                $arguments[] = $argument;
                $argument = '';

                continue;
            }

            $argument .= $char;
        }

        if ($argument !== '') {
            $arguments[] = $argument;
        }


        $result = array();
        foreach ($arguments as $index => $argument) {
            $argument = trim($argument);
            if ($argument === '') {
                continue;
            }

            $result[] = $argument;
        }

        return $result;
    }

    /**
     * Parse the arguments for the command line interface
     *
     * <p>This method will parse the arguments which can be passed in different
     * ways: variables, flags and/or values.</p>
     * <ul>
     * <li>--named-boolean</li>
     * <li>--named-variable="your value"</li>
     * <li>-f</li>
     * <li>-afc</li>
     * <li>plain values</li>
     * </ul>
     *
     * <p>An example:<br />
     * <p>index.php agenda/event/15 --detail --comments=no --title="Agenda events"
     *  -afc nice</p>
     * <p>will result in</p>
     * <p>array(<br />
     * &nbsp;&nbsp;&nbsp;&nbsp;0 =&gt; "agenda/event/15"<br />
     * &nbsp;&nbsp;&nbsp;&nbsp;'detail' =&gt; true<br />
     * &nbsp;&nbsp;&nbsp;&nbsp;'comments' =&gt; no<br />
     * &nbsp;&nbsp;&nbsp;&nbsp;'title' =&gt; "Agenda events"<br />
     * &nbsp;&nbsp;&nbsp;&nbsp;'a' =&gt; true<br />
     * &nbsp;&nbsp;&nbsp;&nbsp;'f' =&gt; true<br />
     * &nbsp;&nbsp;&nbsp;&nbsp;'c' =&gt; true<br />
     * &nbsp;&nbsp;&nbsp;&nbsp;1 =&gt; "nice"<br />
     * )
     * </p>
     *
     * parseArgs Command Line Interface (CLI) utility function.
     * @author              Patrick Fisher <patrick@pwfisher.com>
     * @source              https://github.com/pwfisher/CommandLine.php
     *
     * @param array $arguments The arguments from the command line
     * @return array Parsed arguments
     */
    public function parseArguments(array $arguments) {
        $parsedArguments = array();

        foreach ($arguments as $argument) {
            if (substr($argument, 0, 2) == '--') {
                // variables: --key=value or --key
                $eqPos = strpos($argument, '=');
                if ($eqPos === false) {
                    $key = substr($argument, 2);
                    if (!isset($parsedArguments[$key])) {
                        $parsedArguments[$key] = true;
                    }
                } else {
                    $key = substr($argument, 2, $eqPos - 2);
                    $parsedArguments[$key] = substr($argument, $eqPos + 1);
                }
            } elseif (substr($argument, 0, 1) == '-') {
                // flags: -n or -arf
                if (substr($argument, 2, 1) == '='){
                    $key = substr($argument, 1, 1);
                    $parsedArguments[$key] = substr($argument, 3);
                } else {
                    $flags = str_split(substr($argument, 1));
                    foreach ($flags as $flag){
                        if (!isset($parsedArguments[$flag])) {
                            $parsedArguments[$flag] = true;
                        }
                    }
                }
            } else {
                // values
                $parsedArguments[] = $argument;
            }
        }

        return $parsedArguments;
    }

}

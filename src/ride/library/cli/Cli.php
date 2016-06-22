<?php

namespace ride\library\cli;

use ride\library\cli\command\ExitCommand;
use ride\library\cli\command\HelpCommand;
use ride\library\cli\command\PhpCommand;
use ride\library\cli\exception\CliException;
use ride\library\cli\input\AutoCompletableInput;
use ride\library\cli\input\Input;
use ride\library\cli\output\Output;

use \Exception;

/**
 * Command Line Interface
 */
class Cli {

    /**
     * Instance of the command interpreter
     * @var CommandInterpreter
     */
    protected $interpreter;

    /**
     * Input implementation for the commands
     * @var \ride\library\cli\input\Input
     */
	protected $input;

    /**
     * Output implementation
     * @var \ride\library\cli\output\Output
     */
	protected $output;

    /**
     * Prompt for the CLI input
     * @var string
     */
    protected $prompt;

    /**
     * Flag to see if debug mode is enabled
     * @var boolean
     */
    protected $isDebug;

    /**
     * Flag to see if the PHP command should be enabled
     * @var boolean
     */
    protected $enablePhpCommand;

    /**
     * Exit code for the script
     * @var integer
     */
    protected $exitCode;

    /**
     * Constructs a new command line interface
     * @param CommandInterpreter $interpreter
     * @param string $prompt Prompt for the input
     * @return null
     */
    public function __construct(CommandInterpreter $interpreter, $prompt = '> ') {
    	$this->input = null;
    	$this->output = null;
    	$this->interpreter = $interpreter;
    	$this->prompt = $prompt;
    	$this->isDebug = false;
    	$this->enablePhpCommand = false;
    	$this->exitCode = 0;
    }

    /**
     * Sets the input implementation which is available for the commands
     * @param \ride\library\cli\input\Input $input
     * @return null
     */
    public function setInput(Input $input) {
        $this->input = $input;
    }

    /**
     * Gets the input implementation which is available for the commands
     * @return \ride\library\cli\input\Input
     */
    public function getInput() {
        return $this->input;
    }

    /**
     * Sets the output implementation
     * @param \ride\library\cli\output\Output $output
     * @return null
     */
    public function setOutput(Output $output) {
    	$this->output = $output;
    }

    /**
     * Gets the output implementation
     * @return \ride\library\cli\output\Output
     */
    public function getOutput() {
    	return $this->output;
    }

    /**
     * Sets the command interpreter
     * @param \ride\library\cli\CommandInterpreter $commandInterpreter
     * @return null
     */
    public function setCommandInterpreter(CommandInterpreter $commandInterpreter) {
        $this->interpreter = $commandInterpreter;
    }

    /**
     * Gets the command interpreter
     * @return \ride\library\cli\CommandInterpreter
     */
    public function getCommandInterpreter() {
        return $this->interpreter;
    }

    /**
     * Sets the debug flag
     * @param boolean $isDebug
     * @return null
     */
    public function setIsDebug($isDebug) {
        $this->isDebug = $isDebug;
    }

    /**
     * Checks if the debug flag is on
     * @return boolean
     */
    public function isDebug() {
        return $this->isDebug;
    }

    /**
     * Sets whether to enable the builtin php command
     * @param boolean $enablePhpCommand
     * @return null
     */
    public function setEnablePhpCommand($enablePhpCommand) {
        $this->enablePhpCommand = $enablePhpCommand;
    }

    /**
     * Checks whether the builtin php command is enabled
     * @return boolean
     */
    public function isPhpCommandEnabled() {
        return $this->enablePhpCommand;
    }

    /**
     * Gets the exit code for the script
     * @return integer
     */
    public function getExitCode() {
        return $this->exitCode;
    }

    /**
     * Runs the console
     * @param \ride\library\cli\input\Input $input Input for the CLI
     * @return null
     * @throws \ride\library\cli\exception\CliException when no input or output
     * is set to this CLI
     */
    public function run(Input $input) {
        // check the input and output
        if (!$this->input) {
            throw new CliException('Could not run the CLI: no input set, invoke setInput first');
        } elseif (!$this->output) {
            throw new CliException('Could not run the CLI: no output set, invoke setOutput first');
        }

        // initialize the interpreter
        $this->initialize($input);

        // run the interpreter loop
        do {
            // get the input
            $command = $input->read($this->output, $this->prompt);
            if ($command === false && $input->isInteractive()) {
                // CTRL+D
                $this->output->writeLine('');

                $command = ExitCommand::NAME;
            }

            $command = trim($command);

            if ($command == ExitCommand::NAME || $command == '') {
                // empty or exit command, next loop
                continue;
            }

            $this->exitCode = 0;

            if ($this->enablePhpCommand && strlen($command) > 4 && substr($command, 0, 4) == 'php ') {
                // implement the php command straight in the console in order to
                // create/use a context
                $command = substr($command, 4);

                if (substr($command, -1) != ';') {
                    $command .= ';';
                }

                try {
                    eval($command);
                } catch (Exception $exception) {
                    $this->exitCode = 1;

                    $this->output->writeErrorLine('PHP interpreter: ' . $exception->getMessage());
                }
            } else {
                try {
                    $this->interpreter->interpret($command, $this->input, $this->output);
                } catch (Exception $exception) {
                    $this->exitCode = 1;

                    $message = $exception->getMessage();
                    if (!$message) {
                        $message = get_class($exception);
                    }

                    $this->output->writeErrorLine('Error: ' . $message);
                    if ($this->isDebug) {
                        do {
                            $this->output->writeErrorLine($exception->getTraceAsString());

                            $exception = $exception->getPrevious();
                            if (!$exception) {
                                continue;
                            }

                            $message = $exception->getMessage();
                            if (!$message) {
                                $message = get_class($exception);
                            }

                            $this->output->writeErrorLine('Caused by: ' . $message);
                        } while ($exception);
                    }
                }
            }
        } while ($input->isInteractive() && $command != ExitCommand::NAME);
    }

    /**
     * Hook to initialize the CLI
     * @param \ride\library\cli\input\Input $input Input of the CLI
     * @return null
     */
    protected function initialize(Input $input) {
        // initialize the command container with builtin commands
        $commandContainer = $this->interpreter->getCommandContainer();

        if ($input->isInteractive() && !$commandContainer->hasCommand(ExitCommand::NAME)) {
            $commandContainer->addCommand(new ExitCommand());
        }

        if (!$commandContainer->hasCommand(HelpCommand::NAME)) {
            $commandContainer->addCommand(new HelpCommand($commandContainer));
        }

        if ($this->enablePhpCommand && !$commandContainer->hasCommand(PhpCommand::NAME)) {
            $commandContainer->addCommand(new PhpCommand());
        }

        // initialize auto completion
        if ($input instanceof AutoCompletableInput) {
            $input->addAutoCompletion($this->interpreter->getCommandContainer());
        }
    }

}

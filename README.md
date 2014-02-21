# Ride: CLI Library

CLI library of the PHP Ride framework.

## Command

The _Command_ interface is what you will implement to extend the CLI.
It contains the logic for your action.

You can check this code sample of a command:

    <?php
    
    use ride\library\cli\command\AbstractCommand;

    class MyCommand extends AbstractCommand {
    
        public function __construct() {
            parent::__construct('name', 'description');
            
            // Define a required argument
            $this->addArgument('arg1', 'description');
            
            // Define a optional argument
            $this->addArgument('arg2', 'description', false);

            // Define a dynamic argument
            // The value is the rest of the command line.
            // There are no more arguments allowed after a dynamic one
            $this->addArgument('arg3', 'description', false, true);
            
            // Define a flag (always optional)
            $this->addFlag('flag', 'description');
        }
        
        public function execute() {
            // Get the defined input values
            $arg1 = $this->input->getArgument('arg1');
            $arg2 = $this->input->getArgument('arg2', 'default');
            $arg3 = $this->input->getArgument('arg3');
            $flag = $this->input->getFlag('flag');

            if ($this->input->isInteractive()) {
                // Interactive shell, read some input interactive
                $arg4 = $this->input->read($this->output, 'my prompt: ');
            }            
            
            // Write some output
            $this->output->write("output");
            $this->output->writeLine("output line");
            $this->output->writeError("error output");
            $this->output->writeErrorLine("error output line");
        }
        
    }

## Code Sample

Check this code sample to implement this library:

    <?php
    
    use ride\library\cli\command\CommandContainer;
    use ride\library\cli\input\PhpInput;
    use ride\library\cli\output\PhpOutput;
    use ride\library\cli\Cli;
    use ride\library\cli\CommandInterpreter;
    
    // Create a command container and add some commands to it
    $commandContainer = new CommandContainer();
    $commandContainer->addCommand(new MyCommand()); // check the command code above
    
    // Create a command interpreter from the container
    $commandInterpreter = new CommandInterpreter($commandContainer);
    
    // Create your input and output interface
    $input = new PhpInput(); // ReadlineInput for interactive shell with auto completion and history 
    $output = new PhpOutput();
    
    // and create and run the cli
    $cli = new Cli($commandInterpreter);
    $cli->setInput($input); // input for the commands
    $cli->setOutput($output);
    
    $cli->run($input); // input for the CLI, you can use ArgumentInput to parse the command line
    
    // exit with a proper code
    exit($cli->getExitCode());
<?php

namespace ride\library\cli\command;

use ride\library\cli\input\CommandInput;
use ride\library\cli\output\ArrayOutput;

use \PHPUnit_Framework_TestCase;

class HelpCommandTest extends PHPUnit_Framework_TestCase {

    public function testExecuteNoArgument() {
        $commandContainer = new CommandContainer();

        $exitCommand = new ExitCommand();
        $helpCommand = new HelpCommand($commandContainer);

        $commandContainer->addCommand($exitCommand);
        $commandContainer->addCommand($helpCommand);

        $input = new CommandInput('help', 'help');
        $output = new ArrayOutput();

        $helpCommand->setCommandInput($input);
        $helpCommand->setOutput($output);

        $helpCommand->execute();

        $expected =
"Available commands:
- exit
- help [<command>]

Use 'help <command>' to get help for a specific command.
";

        $this->assertEquals($expected, implode("\n", $output->getOutput()));
    }

    public function testExecuteWithArgument() {
        $commandContainer = new CommandContainer();

        $helpCommand = new HelpCommand($commandContainer);

        $commandContainer->addCommand($helpCommand);

        $input = new CommandInput('help help', 'help', array('command' => 'help'));
        $output = new ArrayOutput();

        $helpCommand->setCommandInput($input);
        $helpCommand->setOutput($output);

        $helpCommand->execute();

        $expected =
"Prints this help.

Syntax: help [<command>]
- [<command>] Provide a name of a command to get the detailed help of the command
";

        $this->assertEquals($expected, implode("\n", $output->getOutput()));
    }

}

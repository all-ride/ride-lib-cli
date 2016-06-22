<?php

namespace ride\library\cli;

use ride\library\cli\command\CommandContainer;
use ride\library\cli\command\ExitCommand;
use ride\library\cli\command\HelpCommand;
use ride\library\cli\command\TestCommand;
use ride\library\cli\input\PhpInput;
use ride\library\cli\output\PhpOutput;

use \PHPUnit_Framework_TestCase;

class CommandInterpreterTest extends PHPUnit_Framework_TestCase {

    public function setUp() {
        $commandContainer = new CommandContainer();

        $exitCommand = new ExitCommand();
        $helpCommand = new HelpCommand($commandContainer);
        $this->testCommand = new TestCommand();

        $commandContainer->addCommand($exitCommand);
        $commandContainer->addCommand($helpCommand);
        $commandContainer->addCommand($this->testCommand);

        $this->commandInterpreter = new CommandInterpreter($commandContainer);

        $this->input = new PhpInput();
        $this->output = new PhpOutput();
    }

    public function testInterpretInvokesCommand() {
        $this->assertFalse($this->testCommand->isInvoked());

        $this->commandInterpreter->interpret('name required', $this->input, $this->output);

        $this->assertTrue($this->testCommand->isInvoked());
    }

    public function testInterpretInvokesAlias() {
        $this->assertFalse($this->testCommand->isInvoked());

        $this->commandInterpreter->interpret('n required', $this->input, $this->output);

        $this->assertTrue($this->testCommand->isInvoked());
    }

    /**
     * @expectedException ride\library\cli\exception\CommandNotFoundException
     */
    public function testInterpretThrowsExceptionWhenCommandNotFound() {
        $this->commandInterpreter->interpret('foo', $this->input, $this->output);
    }

    /**
     * @expectedException ride\library\cli\exception\ArgumentNotSetException
     */
    public function testInterpretThrowsExceptionWhenArgumentNotSet() {
        $this->commandInterpreter->interpret('name', $this->input, $this->output);
    }

    /**
     * @expectedException ride\library\cli\exception\InvalidFlagException
     */
    public function testInterpretThrowsExceptionWhenFlagNotFound() {
        $this->commandInterpreter->interpret('name required --unexisting-flag', $this->input, $this->output);
    }

    /**
     * @expectedException ride\library\cli\exception\InvalidArgumentCountException
     */
    public function testInterpretThrowsExceptionWhenInvalidArgumentCount() {
        $this->commandInterpreter->interpret('exit arg1', $this->input, $this->output);
    }

}

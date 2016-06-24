<?php

namespace ride\library\cli;

use ride\library\cli\command\CommandContainer;
use ride\library\cli\command\HelpCommand;
use ride\library\cli\command\TestCommand;
use ride\library\cli\input\ArrayInput;
use ride\library\cli\input\PhpInput;
use ride\library\cli\output\ArrayOutput;

use \PHPUnit_Framework_TestCase;

class CliTest extends PHPUnit_Framework_TestCase {

    public function setUp() {
        $this->testCommand = new TestCommand();

        $this->commandContainer = new CommandContainer();
        $this->commandContainer->addCommand($this->testCommand);

        $this->commandInterpreter = new CommandInterpreter($this->commandContainer);
        $this->cli = new Cli($this->commandInterpreter);
        $this->cli->setInput(new ArrayInput(array()));
        $this->cli->setOutput(new ArrayOutput());
    }

    public function testConstruct() {
        $commandContainer = $this->getMock(CommandContainer::class);
        $commandInterpreter = $this->getMockBuilder(CommandInterpreter::class)
                                   ->setConstructorArgs(array($commandContainer))
                                   ->getMock();
        $prompt = '$ ';

        $cli = new Cli($commandInterpreter, $prompt);

        $this->assertEquals($commandInterpreter, $cli->getCommandInterpreter());
        $this->assertEquals($prompt, $cli->getPrompt());
        $this->assertEquals(0, $cli->getExitCode());
        $this->assertNull($cli->getInput());
        $this->assertNull($cli->getOutput());
        $this->assertFalse($cli->isDebug());
        $this->assertFalse($cli->isPhpCommandEnabled());
    }

    public function testRun() {
        $commands = array(
            'name required',
            'help name',
        );
        $expectedOutput = array(
            'description',
            '',
            'Syntax: name [--flag] <required> [<optional> [<dynamic>]]',
            'Aliases: n',
            '- [--flag] flag description',
            '- <required> required description',
            '- [<optional>] optional description',
            '- [<dynamic>] dynamic description',
            '',
        );

        $this->assertFalse($this->testCommand->isInvoked());

        $this->cli->run(new ArrayInput($commands));

        $this->assertEquals($expectedOutput, $this->cli->getOutput()->getOutput());
        $this->assertEquals(0, $this->cli->getExitCode());
        $this->assertTrue($this->testCommand->isInvoked());
    }

    public function testRunStopsOnExit() {
        $commands = array(
            'exit',
            'name required',
        );
        $expectedOutput = array();

        $this->assertFalse($this->testCommand->isInvoked());

        $this->cli->run(new ArrayInput($commands));

        $this->assertEquals($expectedOutput, $this->cli->getOutput()->getOutput());
        $this->assertEquals(0, $this->cli->getExitCode());
        $this->assertFalse($this->testCommand->isInvoked());
    }

    public function testRunWithPhpCommand() {
        $this->cli->setEnablePhpCommand(true);

        $commands = array(
            'php $var = 1',
            'php $var /= 0',
            'php $var += 3',
            'php $this->output->write($var)',
        );
        $expectedOutput = array(
            '3',
        );
        $expectedErrorOutput = array(
            'PHP interpreter: Division by zero',
        );

        $this->cli->run(new ArrayInput($commands));

        $this->assertEquals($expectedOutput, $this->cli->getOutput()->getOutput());
        $this->assertEquals($expectedErrorOutput, $this->cli->getOutput()->getErrorOutput());
        $this->assertEquals(0, $this->cli->getExitCode());
    }

    public function testRunDisplaysError() {
        $commands = array(
            'name',
            'unknown',
        );
        $expectedOutput = array(
            'Error: No required provided',
            'Error: Command not found: unknown',
        );

        $this->cli->run(new ArrayInput($commands));


        $this->assertEquals($expectedOutput, $this->cli->getOutput()->getErrorOutput());
        $this->assertEquals(1, $this->cli->getExitCode());
    }

    public function testRunDisplaysExceptionWhenDebugIsEnabled() {
        $this->cli->setIsDebug(true);

        $commands = array(
            'name',
        );
        $expectedOutput = array(
            'Error: No required provided',
        );

        $this->cli->run(new ArrayInput($commands));

        $output = $this->cli->getOutput()->getErrorOutput();

        $this->assertEquals($expectedOutput[0], $output[0]);
        $this->assertTrue(count($output) > 1);
        $this->assertEquals(1, $this->cli->getExitCode());
    }

    /**
     * @expectedException ride\library\cli\exception\CliException
     */
    public function testRunThrowsExceptionWhenNoInputSet() {
        $commandContainer = $this->getMock(CommandContainer::class);
        $commandInterpreter = $this->getMockBuilder(CommandInterpreter::class)
                                   ->setConstructorArgs(array($commandContainer))
                                   ->getMock();

        $cli = new Cli($commandInterpreter);
        $cli->setOutput(new ArrayOutput());

        $cli->run(new ArrayInput(array()));
    }

    /**
     * @expectedException ride\library\cli\exception\CliException
     */
    public function testRunThrowsExceptionWhenNoOutputSet() {
        $commandContainer = $this->getMock(CommandContainer::class);
        $commandInterpreter = $this->getMockBuilder(CommandInterpreter::class)
                                   ->setConstructorArgs(array($commandContainer))
                                   ->getMock();

        $cli = new Cli($commandInterpreter);
        $cli->setInput(new ArrayInput(array()));

        $cli->run(new ArrayInput(array()));
    }

    public function testRunAddsAutoCompleteToInteractiveInput() {
        $input = $this->getMock('ride\\library\\cli\\input\\AutoCompletableInput', array('isInteractive', 'read', 'addAutoCompletion'));
        $input->expects($this->any())
              ->method('isInteractive')
              ->will($this->returnValue(true));
        $input->expects($this->any())
              ->method('read')
              ->with($this->equalTo($this->cli->getOutput()), $this->equalTo($this->cli->getPrompt()))
              ->will($this->returnValue('exit'));
        $input->expects($this->once())
              ->method('addAutoCompletion')
              ->with($this->equalTo($this->commandContainer))
              ->will($this->returnValue(null));

        $this->cli->run($input);
    }

    public function testRunAddsExitCommandForInteractiveInput() {
        $input = $this->getMock('ride\\library\\cli\\input\\Input', array('isInteractive', 'read'));
        $input->expects($this->any())
              ->method('isInteractive')
              ->will($this->returnValue(true));
        $input->expects($this->any())
              ->method('read')
              ->with($this->equalTo($this->cli->getOutput()), $this->equalTo($this->cli->getPrompt()))
              ->will($this->returnValue('exit'));

        $this->assertFalse($this->commandContainer->hasCommand('exit'));

        $this->cli->run($input);

        $this->assertTrue($this->commandContainer->hasCommand('exit'));
    }

    public function testRunSkipsExitCommandForNonInteractiveInput() {
        $this->assertFalse($this->commandContainer->hasCommand('exit'));

        $this->cli->run(new ArrayInput(array()));

        $this->assertFalse($this->commandContainer->hasCommand('exit'));
    }

    public function testRunAddsPhpCommandWhenEnabled() {
        $this->assertFalse($this->commandContainer->hasCommand('php'));

        $this->cli->setEnablePhpCommand(true);
        $this->cli->run(new ArrayInput(array()));

        $this->assertTrue($this->commandContainer->hasCommand('php'));
    }

    public function testRunSkipsPhpCommandWhenDisabled() {
        $this->assertFalse($this->commandContainer->hasCommand('php'));

        $this->cli->run(new ArrayInput(array()));

        $this->assertFalse($this->commandContainer->hasCommand('php'));
    }

    public function testRunAddsHelpCommand() {
        $this->assertFalse($this->commandContainer->hasCommand('help'));

        $this->cli->run(new ArrayInput(array()));

        $this->assertTrue($this->commandContainer->hasCommand('help'));
    }

}

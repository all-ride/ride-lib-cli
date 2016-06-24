<?php

namespace ride\library\cli\command;

use ride\library\cli\command\ExitCommand;
use ride\library\cli\command\TestCommand;

use \PHPUnit_Framework_TestCase;

class CommandContainerTest extends PHPUnit_Framework_TestCase {

    public function setUp() {
        $this->commandContainer = new CommandContainer();
    }

    public function testCommands() {
        $this->assertEquals(array(), $this->commandContainer->getCommands());
        $this->assertFalse($this->commandContainer->hasCommand('name'));

        $command = new TestCommand();

        $this->commandContainer->addCommand($command);

        $this->assertEquals(array('name' => $command), $this->commandContainer->getCommands());
        $this->assertEquals($command, $this->commandContainer->getCommand('name'));
        $this->assertTrue($this->commandContainer->hasCommand('name'));

        $this->commandContainer->removeCommand('name');

        $this->assertEquals(array(), $this->commandContainer->getCommands());
        $this->assertFalse($this->commandContainer->hasCommand('name'));
    }

    /**
     * @expectedException ride\library\cli\exception\CliException
     */
    public function testGetCommandThrowsExceptionWhenNotFound() {
        $this->commandContainer->getCommand('name');
    }

    /**
     * @dataProvider providerReplaceAliases
     */
    public function testReplaceAliases($expected, $input) {
        $this->commandContainer->addCommand(new TestCommand());

        $this->assertEquals($expected, $this->commandContainer->replaceAliases($input));
    }

    public function providerReplaceAliases() {
        return array(
            array('name', 'n'),
            array('name arg1 arg2', 'n arg1 arg2'),
            array('foo arg1 arg2', 'foo arg1 arg2'),
        );
    }

    /**
     * @dataProvider providerAutoComplete
     */
    public function testAutoComplete($expected, $input) {
        $exitCommand = new ExitCommand();
        $helpCommand = new HelpCommand($this->commandContainer);
        $testCommand = new TestCommand();

        $this->commandContainer->addCommand($exitCommand);
        $this->commandContainer->addCommand($helpCommand);
        $this->commandContainer->addCommand($testCommand);

        $this->assertEquals($expected, $this->commandContainer->autoComplete($input));
    }

    public function providerAutoComplete() {
        return array(
            array(array('exit' => 'exit', 'help' => 'help', 'name' => 'name'), ''),
            array(array('name' => 'name'), 'n'),
            array(array('help' => 'help'), 'he'),
            array(array('help exit' => 'help exit'), 'help e'),
        );
    }

    public function testIterator() {
        $expected = array(
            'exit' => new ExitCommand(),
            'name' => new TestCommand(),
        );

        $this->commandContainer->addCommand($expected['exit']);
        $this->commandContainer->addCommand($expected['name']);

        foreach ($this->commandContainer as $command) {
            $result[$command->getName()] = $command;
        }

        $this->assertEquals($expected, $result);
    }

}

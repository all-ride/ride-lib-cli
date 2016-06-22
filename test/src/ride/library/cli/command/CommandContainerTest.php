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

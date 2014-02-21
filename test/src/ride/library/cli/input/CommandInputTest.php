<?php

namespace ride\library\cli\input;

use \PHPUnit_Framework_TestCase;

class CommandInputTest extends PHPUnit_Framework_TestCase {

    public function testCommandInput() {
        $fullCommand = 'full command';
        $command = 'command';
        $arguments = array('arg' => 'value');
        $flags = array('a' => true, 'b' => false);

        $input = new CommandInput($fullCommand, $command, $arguments, $flags);

        $this->assertEquals($fullCommand, $input->getFullCommand());
        $this->assertEquals($command, $input->getCommand());
        $this->assertEquals($arguments, $input->getArguments());
        $this->assertEquals(1, $input->getArgumentCount());
        $this->assertEquals('value', $input->getArgument('arg'));
        $this->assertEquals('default', $input->getArgument('arg2', 'default'));
        $this->assertTrue($input->hasArguments());
        $this->assertNull($input->getArgument('arg3'));
        $this->assertTrue($input->hasArgument('arg'));
        $this->assertFalse($input->hasArgument('arg2'));
        $this->assertEquals($flags, $input->getFlags());
        $this->assertTrue($input->hasFlags());
        $this->assertTrue($input->getFlag('a'));
        $this->assertFalse($input->getFlag('b'));
        $this->assertNull($input->getFlag('c'));
        $this->assertTrue($input->hasFlag('a'));
        $this->assertTrue($input->hasFlag('b'));
        $this->assertFalse($input->hasFlag('c'));
    }

    public function testNameArguments() {
        $arguments = array(0 => 'value', 1 => 'value2');

        $input = new CommandInput('full', 'command', $arguments);

        $input->nameArgument(0, 'arg1');
        $input->nameArgument(1, 'arg2');

        $expected = array('arg1' => 'value', 'arg2' => 'value2');

        $this->assertEquals($expected, $input->getArguments());

        $input = new CommandInput('full', 'command', $arguments);
        $input->nameDynamicArgument(0, 'arg1');

        $expected = array('arg1' => 'value value2');

        $this->assertEquals($expected, $input->getArguments());
    }

    /**
     * @expectedException ride\library\cli\exception\CliException
     */
    public function testNameArgumentsThrowsExceptionWhenIndexNotSet() {
        $arguments = array(0 => 'value', 1 => 'value2');

        $input = new CommandInput('full', 'command', $arguments);

        $input->nameArgument('undefined', 'arg1');
    }

    public function testInput() {
        $readValue = 'read';
        $prompt = ':';

        $output = $this->getMock('ride\\library\\cli\\output\\Output', array('write', 'writeLine', 'writeError', 'writeErrorLine'));

        $input = $this->getMock('ride\\library\\cli\\input\\Input', array('isInteractive', 'read'));
        $input->expects($this->once())->method('isInteractive')->will($this->returnValue(true));
        $input->expects($this->once())->method('read')->with($this->equalTo($output), $this->equalTo($prompt))->will($this->returnValue($readValue));

        $command = new CommandInput('full');
        $command->setInput($input);

        $this->assertTrue($command->isInteractive());
        $this->assertEquals($readValue, $command->read($output, $prompt));
    }

    /**
     * @expectedException ride\library\cli\exception\CliException
     */
    public function testIsInteractiveThrowsExceptionWhenInputNotSet() {
        $input = new CommandInput('command');
        $input->isInteractive();
    }

    /**
     * @expectedException ride\library\cli\exception\CliException
     */
    public function testReadThrowsExceptionWhenInputNotSet() {
        $output = $this->getMock('ride\\library\\cli\\output\\Output', array('write', 'writeLine', 'writeError', 'writeErrorLine'));

        $input = new CommandInput('command');
        $input->read($output, ':');
    }

}
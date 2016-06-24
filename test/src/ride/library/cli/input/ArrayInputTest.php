<?php

namespace ride\library\cli\input;

use \PHPUnit_Framework_TestCase;

class ArrayInputTest extends PHPUnit_Framework_TestCase {

    /**
     * @var ride\library\cli\input\ArrayInput
     */
    protected $ai;

    public function setUp() {
        $this->commands = array(
            'command1',
            'command2',
            'command3',
        );

        $this->ai = new ArrayInput($this->commands);
    }

    public function testIsInteractive() {
        $this->assertFalse($this->ai->isInteractive());
    }

    public function testRead() {
        $output = $this->getMock('ride\\library\\cli\\output\\Output', array('write', 'writeLine', 'writeError', 'writeErrorLine'));

        $this->assertEquals('command1', $this->ai->read($output, ':'));
        $this->assertEquals('command2', $this->ai->read($output, ':'));
        $this->assertEquals('command3', $this->ai->read($output, ':'));
        $this->assertNull($this->ai->read($output, ':'));
        $this->assertNull($this->ai->read($output, ':'));
        $this->assertNull($this->ai->read($output, ':'));
    }

}

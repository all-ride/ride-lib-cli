<?php

namespace pallo\library\cli\input;

use \PHPUnit_Framework_TestCase;

class ArgumentInputTest extends PHPUnit_Framework_TestCase {

    /**
     * @var pallo\library\cli\input\ArgumentInput
     */
    protected $ai;

    public function setUp() {
        $this->ai = new ArgumentInput();
    }

    public function testIsInteractive() {
        $this->assertFalse($this->ai->isInteractive());
    }

    public function testRead() {
        $output = $this->getMock('pallo\\library\\cli\\output\\Output', array('write', 'writeLine', 'writeError', 'writeErrorLine'));

        $_SERVER['argv'] = array();

        $this->assertEquals('help', $this->ai->read($output, ':'));
        $this->assertNull($this->ai->read($output, ':'));
        $this->assertNull($this->ai->read($output, ':'));
        $this->assertNull($this->ai->read($output, ':'));
    }

    public function testReadWithServerArguments() {
        $output = $this->getMock('pallo\\library\\cli\\output\\Output', array('write', 'writeLine', 'writeError', 'writeErrorLine'));

        $_SERVER['argv'] = array(
        	'value',
        	'value "test"',
        );

        $expected = 'value "value \\"test\\""';

        $this->assertEquals($expected, $this->ai->read($output, ':'));
        $this->assertNull($this->ai->read($output, ':'));
        $this->assertNull($this->ai->read($output, ':'));
        $this->assertNull($this->ai->read($output, ':'));
    }

}
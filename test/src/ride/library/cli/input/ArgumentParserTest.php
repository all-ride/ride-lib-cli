<?php

namespace ride\library\cli\input;

use \PHPUnit_Framework_TestCase;

class ArgumentParserTest extends PHPUnit_Framework_TestCase {

    /**
     * @var ride\library\cli\input\ArgumentParser
     */
    protected $ap;

    public function setUp() {
        $this->ap = new ArgumentParser();
    }

    public function testGetCommandInput() {
        $result = $this->ap->getCommandInput('command');

        $this->assertEquals(new CommandInput('command'), $result);

        $input = 'command subcommand argument1 --flag1=value';
        $offset = 1;

        $result = $this->ap->getCommandInput($input, $offset);

        $expected = new CommandInput($input, 'command subcommand', array(0 => 'argument1'), array('flag1' => 'value'));

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider providerGetArgumentsThrowsExceptionWhenInvalidArgumentProvided
     * @expectedException ride\library\cli\exception\CliException
     */
    public function testGetArgumentsThrowsExceptionWhenInvalidArgumentProvided($arguments) {
        $this->ap->getArguments($arguments);
    }

    public function providerGetArgumentsThrowsExceptionWhenInvalidArgumentProvided() {
        return array(
        	array(array()),
        	array($this),
        );
    }

    /**
     * @dataProvider providerGetArguments
     */
    public function testGetArguments(array $expected, $arguments) {
        $this->assertEquals($expected, $this->ap->getArguments($arguments));
    }

    public function providerGetArguments() {
        return array(
            array(
                array(
                    0 => 'argument',
                    '--var=test "value"',
                    '-a',
                    '-b=true',
                    '-cd',
                    '--ef',
                ),
                'argument --var="test \"value\""  -a -b=true -cd --ef',
            ),
        );
    }

    /**
     * @dataProvider providerParseArguments
     */
    public function testParseArguments(array $expected, array $arguments) {
        $this->assertEquals($expected, $this->ap->parseArguments($arguments));
    }

    public function providerParseArguments() {
        return array(
            array(
                array(
                    0 => 'admin/system',
                ),
                array(
                	'admin/system',
            	),
        	),
            array(
                array(
                    0 => 'admin/system',
                    'path' => '/path/to/something',
                ),
                array(
                    'admin/system',
                	'--path=/path/to/something',
            	),
        	),
            array(
                array(
                    0 => 'admin/system',
                    'a' => true,
                    'b' => true,
                    'c' => true,
                    'd' => 'value',
                    'e' => true,
                    'fg' => true,
                    'h' => 'test',
                    'ij' => 'test',
                ),
                array(
                	'admin/system',
                	'-a',
                	'-bc',
                    '-d=value',
                	'--e',
                	'--fg',
                	'--h=test',
                	'--ij=test',
            	),
        	),
        );
    }

}
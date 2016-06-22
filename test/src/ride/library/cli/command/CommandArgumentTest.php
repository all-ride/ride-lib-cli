<?php

namespace ride\library\cli\command;

use \PHPUnit_Framework_TestCase;

class CommandArgumentTest extends PHPUnit_Framework_TestCase {

    public function testConstruct() {
        $name = 'name';
        $description = 'description';
        $isRequired = true;
        $isDynamic = true;

        $argument = new CommandArgument($name, $description, $isRequired, $isDynamic);

        $this->assertEquals($name, $argument->getName());
        $this->assertEquals($description, $argument->getDescription());
        $this->assertEquals($isRequired, $argument->isRequired());
        $this->assertEquals($isDynamic, $argument->isDynamic());
    }

    /**
     * @dataProvider providerConstructThrowsExceptionOnInvalidArgument
     * @expectedException ride\library\cli\exception\CliException
     */
    public function testConstructThrowsExceptionOnInvalidArgument($name, $description, $isRequired, $isDynamic) {
        new CommandArgument($name, $description, $isRequired, $isDynamic);
    }

    public function providerConstructThrowsExceptionOnInvalidArgument() {
        return array(
            array(null, 'description', true, true),
            array('name', 'descripion', null, true),
            array('name', 'description', true, null),
            array($this, 'description', true, true),
            array('name', $this, true, true),
            array('name', 'descripion', $this, true),
            array('name', 'description', true, $this),
        );
    }

    /**
     * @dataProvider providerToString
     */
    public function testToString($expected, $name, $description, $isRequired, $isDynamic) {
        $argument = new CommandArgument($name, $description, $isRequired, $isDynamic);

        $this->assertEquals($expected, (string) $expected);
    }

    public function providerToString() {
        return array(
            array('<name> description', 'name', 'description', true, false),
            array('[<name>] description', 'name', 'description', false, false),
        );
    }

}

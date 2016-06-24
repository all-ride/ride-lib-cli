<?php

namespace ride\library\cli\command;

use \PHPUnit_Framework_TestCase;

class AbstractCommandTest extends PHPUnit_Framework_TestCase {

    public function testConstruct() {
        $name = 'name';
        $description = 'description';

        $command = $this->getMockBuilder(AbstractCommand::class)
                              ->setConstructorArgs(array($name, $description))
                              ->setMethods(array('execute'))
                              ->getMock();

        $this->assertEquals($name, $command->getName());
        $this->assertEquals($description, $command->getDescription());
        $this->assertEquals(array(), $command->getAliases());
        $this->assertEquals(array(), $command->getArguments());
        $this->assertEquals(array(), $command->getFlags());
    }

    public function testGetSyntax() {
        $command = new TestCommand();
        $syntax = 'name [--flag] <required> [<optional> [<dynamic>]]';

        $this->assertEquals($syntax, $command->getSyntax());
    }

}


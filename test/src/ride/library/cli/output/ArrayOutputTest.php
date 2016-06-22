<?php

namespace ride\library\cli\output;

use \PHPUnit_Framework_TestCase;

class ArrayOutputTest extends PHPUnit_Framework_TestCase {

    public function testOutput() {
        $output = new ArrayOutput();

        $output->write('Te');
        $output->writeLine('st');
        $output->writeError('Err');
        $output->writeErrorLine('or');

        $content = array(
            'Test',
        );

        $this->assertEquals($content, $output->getOutput());

        $content = array(
            'Error',
        );

        $this->assertEquals($content, $output->getErrorOutput());
    }

}

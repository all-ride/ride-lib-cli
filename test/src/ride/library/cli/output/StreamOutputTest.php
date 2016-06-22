<?php

namespace ride\library\cli\output;

use \PHPUnit_Framework_TestCase;

class StreamOutputTest extends PHPUnit_Framework_TestCase {

    public function testOutput() {
        $stream = tmpfile();

        $output = new StreamOutput($stream);

        $output->write('Te');
        $output->writeLine('st');
        $output->writeError('Err');
        $output->writeErrorLine('or');

        $content = "Test\nError\n";

        fseek($stream, 0);
        $this->assertEquals($content, fread($stream, 1024));

        fclose($stream);
    }

    public function testOutputAndError() {
        $outputStream = tmpfile();
        $errorStream = tmpfile();

        $output = new StreamOutput($outputStream, $errorStream);
        $output->write('Te');
        $output->writeLine('st');
        $output->writeError('Err');
        $output->writeErrorLine('or');

        $content = "Test\n";

        fseek($outputStream, 0);
        $this->assertEquals($content, fread($outputStream, 1024));

        $content = "Error\n";

        fseek($errorStream, 0);
        $this->assertEquals($content, fread($errorStream, 1024));

        fclose($outputStream);
    }

}

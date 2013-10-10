<?php

namespace pallo\library\cli\output;

/**
 * Standard PHP implementation of the output interface
 */
class PhpOutput extends StreamOutput {

    /**
     * Constructs the PHP output
     * @return null
     */
    public function __construct() {
        parent::__construct(STDOUT, defined('STDERR') ? STDERR : null);
    }

}
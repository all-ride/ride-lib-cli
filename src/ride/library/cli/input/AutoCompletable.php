<?php

namespace ride\library\cli\input;

/**
 * Interface for auto completion on the input
 */
interface AutoCompletable {

    /**
     * Performs auto complete on the provided input
     * @param string $input Input value to auto complete
     * @return array|null Array with the auto completion matches or null when
     * no auto completion is available
     */
    public function autoComplete($input);

}
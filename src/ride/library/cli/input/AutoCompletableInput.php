<?php

namespace ride\library\cli\input;

/**
 * Interface for a auto completable input
 */
interface AutoCompletableInput extends Input {

    /**
     * Adds a auto completion implementation to the input
     * @param AutoCompletable $autoCompletable
     * @return null
     */
    public function addAutoCompletion(AutoCompletable $autoCompletable);

}
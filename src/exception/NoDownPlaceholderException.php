<?php

namespace mtoolkit\evolution\exception;

class NoDownPlaceholderException extends \Exception
{
    public function __construct($evolutionFilename)
    {
        parent::__construct(sprintf('"-- DOWN" placeholder not found in %s evolution', $evolutionFilename));
    }
}
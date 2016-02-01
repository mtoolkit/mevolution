<?php

namespace mtoolkit\evolution\exception;

class NoUpPlaceholderException extends \Exception
{
    public function __construct($evolutionFilename)
    {
        parent::__construct(sprintf('"-- UP" placeholder not found in %s evolution', $evolutionFilename));
    }
}
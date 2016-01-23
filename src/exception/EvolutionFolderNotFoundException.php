<?php
namespace mtoolkit\evolution\exception;

class EvolutionFolderNotFoundException extends \Exception
{
    public function __construct($filePath)
    {
        parent::__construct(printf("The folder of the evolution %s is not found", $filePath));
    }
}
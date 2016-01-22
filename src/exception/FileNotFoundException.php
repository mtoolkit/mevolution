<?php
namespace mtoolkit\evolution\exception;

class FileNotFoundException extends \Exception
{
    public function __construct($fileName, $code = 0, \Exception $previous = null)
    {
        parent::__construct(sprintf('File %s does not exist', $fileName), $code, $previous);
    }
}
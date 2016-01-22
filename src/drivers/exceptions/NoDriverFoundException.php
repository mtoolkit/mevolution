<?php
namespace mtoolkit\evolution\drivers\exceptions;

/**
 * Class NoDriverFoundException
 * @package mtooolkit\evolution\drivers\exceptions
 */
class NoDriverFoundException extends \Exception
{
    public function __construct($driver, $code = -1, \Exception $previous = null)
    {
        parent::__construct(sprintf('The driver $s does not exist.', $driver), $code, $previous);
    }
}
<?php
namespace mtoolkit\evolution\drivers\exceptions;

/**
 * Class InsertEvolutionException used when it is impossible to insert a new evolution into the table.
 *
 * @package mtooolkit\evolution\drivers\exceptions
 */
class InsertEvolutionException extends \Exception
{
    public function __construct($code = -1, \Exception $previous = null)
    {
        parent::__construct('Impossible to insert an evolution into the table.', $code, $previous);
    }
}
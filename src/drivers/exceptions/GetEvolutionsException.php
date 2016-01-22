<?php
namespace mtoolkit\evolution\drivers\exceptions;

/**
 * Class GetEvolutionsException used when it is impossible to query the evolution table.
 *
 * @package mtooolkit\evolution\drivers\exceptions
 */
class GetEvolutionsException extends \Exception
{
    public function __construct($code = -1, \Exception $previous = null)
    {
        parent::__construct('Evolution table is not accessible. Check the database connection or if the table exists.', $code, $previous);
    }
}
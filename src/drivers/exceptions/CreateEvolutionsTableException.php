<?php
namespace mtoolkit\evolution\drivers\exceptions;

/**
 * Class CreateEvolutionsTableException used when it is impossible to create the table of the evolutions in the database.
 *
 * @package mtooolkit\evolution\drivers\exceptions
 */
class CreateEvolutionsTableException extends \Exception
{
    public function __construct($tableName, $code = -1, \Exception $previous = null)
    {
        parent::__construct(sprintf('Impossible to create the table %s into the database, check the settings file.', $tableName), $code, $previous);
    }
}
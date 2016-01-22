<?php
namespace mtoolkit\evolution\drivers\exceptions;

/**
 * Class CreateEvolutionsTableException used when it is impossible to create the table of the evolutions in the database.
 *
 * @package mtooolkit\evolution\drivers\exceptions
 */
class CleanEvolutionsTableException extends \Exception
{
    public function __construct($tableName, $code = -1, \Exception $previous = null)
    {
        parent::__construct(sprintf('Impossible to clean the table %s into the database, check the settings file.', $tableName), $code, $previous);
    }
}
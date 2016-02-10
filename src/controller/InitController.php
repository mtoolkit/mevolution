<?php

namespace mtoolkit\evolution\controller;

use mtoolkit\evolution\model\evolution\EvolutionFile;

class InitController extends EvolutionController
{
    public function __construct( $settingsFilePath, $evolutionsFolderPath, $to )
    {
        parent::__construct( $settingsFilePath, $evolutionsFolderPath, $to );
    }

    /**
     * Creates the table of the evolutions into the database.<br>
     * Inserts the new evolutions in the table.
     */
    public function init()
    {
        echo 'Creating evolution table...' . PHP_EOL;
        $this->getDriver()->createEvolutionsTable();
        $lastEvolutionId = $this->getDriver()->getLastEvolutionId();

        echo 'Updating evolution table...' . PHP_EOL;
        foreach( $this->getFilePathList() as $filePath )
        {
            $evolution = EvolutionFile::getEvolution( $filePath );

            if( $lastEvolutionId < $evolution->getId() )
            {
                $this->getDriver()->insertEvolution(
                    $evolution->getId(),
                    $evolution->getUp(),
                    $evolution->getDown()
                );
                echo sprintf( "\tInserted evolution %s%s.", $evolution->getId(), PHP_EOL );
            }
        }

        echo PHP_EOL . 'INIT completed without errors.' . PHP_EOL;
    }
}
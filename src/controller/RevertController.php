<?php

namespace mtoolkit\evolution\controller;

class RevertController extends EvolutionController
{
    public function __construct( $settingsFilePath, $evolutionsFolderPath, $to )
    {
        parent::__construct( $settingsFilePath, $evolutionsFolderPath, $to );
    }

    /**
     * Reverts the evolutions.
     */
    public function revert()
    {
        echo 'Reverting evolutions...' . PHP_EOL;

        if( $this->getTo() == null )
        {
            $this->setTo( 0 );
        }

        $this->getDriver()->executeDevolutions( $this->getTo() );

        echo PHP_EOL . 'REVERT completed without errors.' . PHP_EOL;
    }
}
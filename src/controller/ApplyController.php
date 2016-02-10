<?php

namespace mtoolkit\evolution\controller;

class ApplyController extends EvolutionController
{
    public function __construct( $settingsFilePath, $evolutionsFolderPath, $to )
    {
        parent::__construct( $settingsFilePath, $evolutionsFolderPath, $to );
    }

    /**
     * Applies the evolutions.
     */
    public function apply()
    {
        echo 'Applying evolutions...' . PHP_EOL;

        if( $this->getTo() == null )
        {
            $this->setTo( $this->getDriver()->getLastEvolutionId() );
        }

        $this->getDriver()->executeEvolutions( $this->getTo() );

        echo PHP_EOL . 'APPLY completed without errors.' . PHP_EOL;
    }
}
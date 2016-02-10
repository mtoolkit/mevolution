<?php
namespace mtoolkit\evolution;

require_once __DIR__ . '/../libs/autoload.php';

use mtoolkit\evolution\controller\ApplyController;
use mtoolkit\evolution\controller\InitController;
use mtoolkit\evolution\controller\RevertController;
use mtoolkit\evolution\model\commandlineargument\CommandLineArgument;
use mtoolkit\evolution\model\string\StringBook;

class Main
{
    private $argumentList;
    private $command;

    public function __construct( $argumentList )
    {
        $this->argumentList = $argumentList;
    }

    public function run()
    {
        $controller = null;

        try
        {
            $this->checkArgumentCount();
            $this->checkCommand();
            $settingsFilePath = null;
            $evolutionsFolderPath = null;
            $to = null;

            for( $k = 0; $k < count( $this->argumentList ); $k++ )
            {
                $currentArgument = $this->argumentList[$k];

                if( StringBook::is( $currentArgument )->startsWith( CommandLineArgument::ARGUMENT_SETTINGS_FILE_PATH ) )
                {
                    $settingsFilePath = substr( $currentArgument, strlen( CommandLineArgument::ARGUMENT_SETTINGS_FILE_PATH ) );
                    continue;
                }

                if( StringBook::is( $currentArgument )->startsWith( CommandLineArgument::ARGUMENT_EVOLUTIONS_FOLDER_PATH ) )
                {
                    $evolutionsFolderPath = substr( $currentArgument, strlen( CommandLineArgument::ARGUMENT_EVOLUTIONS_FOLDER_PATH ) );
                    continue;
                }

                if( StringBook::is( $currentArgument )->startsWith( CommandLineArgument::ARGUMENT_TO ) )
                {
                    $to = substr( $currentArgument, strlen( CommandLineArgument::ARGUMENT_TO ) );
                    continue;
                }
            }

            if( $settingsFilePath == null || $evolutionsFolderPath == null )
            {
                throw new \Exception(
                    sprintf(
                        'Command, %s and %s are mandatory.',
                        CommandLineArgument::ARGUMENT_EVOLUTIONS_FOLDER_PATH,
                        CommandLineArgument::ARGUMENT_SETTINGS_FILE_PATH
                    )
                );
            }

            switch( $this->command )
            {
                case CommandLineArgument::COMMAND_INIT:
                    $controller = new InitController( $settingsFilePath, $evolutionsFolderPath, $to );
                    $controller->init();
                    break;
                case CommandLineArgument::COMMAND_APPLY:
                    $controller = new ApplyController( $settingsFilePath, $evolutionsFolderPath, $to );
                    $controller->apply();
                    break;
                case CommandLineArgument::COMMAND_REVERT:
                    $controller = new RevertController( $settingsFilePath, $evolutionsFolderPath, $to );
                    $controller->revert();
                    break;
            }

        } catch( \Exception $ex )
        {
            echo PHP_EOL;
            echo "There were some errors:";
            echo PHP_EOL;
            echo sprintf( "\t - %s", $ex->getMessage() );
            echo PHP_EOL;
        } finally
        {
            try
            {
                if( $controller != null )
                {
                    $controller->getDriver()->close();
                }
            } catch( \Exception $ex )
            {

            }
        }
    }

    /**
     * Checks if the number of argument is enought, at least 3.
     *
     * @throws \Exception
     */
    private function checkArgumentCount()
    {
        if( count( $this->argumentList ) < 3 )
        {
            throw new \Exception(
                sprintf(
                    'Command, %s and %s are mandatory.',
                    CommandLineArgument::ARGUMENT_EVOLUTIONS_FOLDER_PATH,
                    CommandLineArgument::ARGUMENT_SETTINGS_FILE_PATH
                )
            );
        }
    }

    /**
     * Checks if the command is valid.
     *
     * @throws \Exception
     */
    private function checkCommand()
    {
        foreach( CommandLineArgument::$COMMAND_LIST as $command )
        {
            foreach( $this->argumentList as $argument )
            {
                if( $command == $argument )
                {
                    $this->command = $argument;

                    return;
                }
            }
        }

        throw new \Exception( sprintf( 'Command not found, use init, apply or revert.' ) );
    }
}

echo 'MEvolution v. 1.2.0' . PHP_EOL . PHP_EOL;
date_default_timezone_set( 'Europe/Rome' );
$main = new Main( $argv );
$main->run();
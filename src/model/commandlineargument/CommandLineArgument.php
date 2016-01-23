<?php
namespace mtoolkit\evolution\model\commandlineargument;

/**
 * Class CommandLineArgument contains all of the valid command line arguments.
 * @package mtoolkit\evolution\controller
 */
final class CommandLineArgument
{
    const COMMAND_INIT='init';
    const COMMAND_HELP='help';
    const COMMAND_APPLY='apply';
    const COMMAND_REVERT='revert';
    const COMMAND_CLEAN='clean';

    public static $COMMAND_LIST=array(
        CommandLineArgument::COMMAND_INIT,
        CommandLineArgument::COMMAND_APPLY,
        CommandLineArgument::COMMAND_HELP,
        CommandLineArgument::COMMAND_REVERT
    );

    const ARGUMENT_EVOLUTIONS_FOLDER_PATH='-e=';
    const ARGUMENT_SETTINGS_FILE_PATH='-s=';
    const ARGUMENT_TO='-to=';
}
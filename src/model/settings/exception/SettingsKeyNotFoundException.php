<?php
namespace mtoolkit\evolution\model\settings\exception;

class SettingsKeyNotFoundException extends \Exception
{
    public function __construct($settingsKey, $code = 0, \Exception $previous = null)
    {
        parent::__construct(
            sprintf('The settings file does not contain manadatory key "%s"', $settingsKey),
            $code,
            $previous);
    }
}
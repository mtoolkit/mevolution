<?php
namespace mtoolkit\evolution\exception;

class SettingsFileNotFoundException extends \Exception
{
    public function __construct($filePath)
    {
        parent::__construct(printf("The settings file %s is not found", $filePath));
    }
}
<?php
namespace mtoolkit\evolution\model\settings;

use mtoolkit\evolution\exception\FileNotFoundException;
use mtoolkit\evolution\model\settings\exception\SettingsKeyNotFoundException;

/**
 * Class SettingsFile parses the settings file and returns a {@link Settings} object.
 *
 * @package mtooolkit\evolution\model\settings
 */
class SettingsFile
{
    const HOST = 'host';
    const PASSWORD = 'password';
    const USERNAME = 'username';
    const TYPE = 'type';
    const DB_NAME = 'dbname';

    public static $KEYS = array(SettingsFile::DB_NAME, SettingsFile::HOST, SettingsFile::PASSWORD, SettingsFile::TYPE, SettingsFile::USERNAME);

    /**
     * @param string $path Relative path of the settings file.
     * @return Settings
     * @throws \Exception
     */
    public static function parse($path)
    {
        $absolutePath=$path;
        $workingFolder = getcwd();

        if( file_exists($absolutePath)==false ) {
            $absolutePath = realpath(sprintf('%s/%s', $workingFolder, $path));
        }

        if (file_exists($absolutePath)===false) {
            throw new FileNotFoundException($absolutePath);
        }

        $array = parse_ini_file($absolutePath);

        foreach (SettingsFile::$KEYS as $key) {
            if (array_key_exists($key, $array) === false) {
                throw new SettingsKeyNotFoundException($key);
            }
        }

        $settings = new Settings();
        $settings->setDbName($array[SettingsFile::DB_NAME])
            ->setHost($array[SettingsFile::HOST])
            ->setPassword($array[SettingsFile::PASSWORD])
            ->setUsername($array[SettingsFile::USERNAME])
            ->setType($array[SettingsFile::TYPE]);

        return $settings;
    }
}
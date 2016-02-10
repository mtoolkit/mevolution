<?php
namespace mtoolkit\evolution\drivers;

use mtoolkit\evolution\drivers\exceptions\NoDriverFoundException;
use mtoolkit\evolution\model\settings\Settings;

/**
 * Class Driver
 * @package mtooolkit\evolution\drivers
 */
class DriverFactory
{
    const MYSQL = 'mysql';

    /**
     * @param Settings $settings
     * @return DatabaseDriver
     * @throws NoDriverFoundException
     */
    public static function get(Settings $settings)
    {
        switch ($settings->getType()) {
            case self::MYSQL:
                return new MySQLDriver($settings);
                break;
        }

        throw new NoDriverFoundException($settings->getType());
    }
}
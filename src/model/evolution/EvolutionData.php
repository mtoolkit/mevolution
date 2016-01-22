<?php
namespace mtoolkit\evolution\model\evolution;

use mtoolkit\evolution\model\settings\Settings;

/**
 * Class EvolutionData
 * @package mtooolkit\evolution\model
 */
abstract class EvolutionData
{
    /**
     * @return Evolution[]
     */
    public abstract function getEvolutions();

    /**
     * @return Settings
     */
    public abstract function getSettings();
}
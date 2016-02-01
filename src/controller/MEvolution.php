<?php
namespace mtoolkit\evolution\controller;

use mtoolkit\evolution\drivers\DatabaseDriver;
use mtoolkit\evolution\drivers\Driver;
use mtoolkit\evolution\exception\EvolutionFolderNotFoundException;
use mtoolkit\evolution\exception\SettingsFileNotFoundException;
use mtoolkit\evolution\model\evolution\EvolutionFile;
use mtoolkit\evolution\model\settings\Settings;
use mtoolkit\evolution\model\settings\SettingsFile;

class MEvolution
{
    /**
     * @var string
     */
    private $settingsFilePath = '';

    /**
     * @var Settings
     */
    private $settings;

    /**
     * @var string
     */
    private $evolutionsFolderPath = '';

    /**
     * @var DatabaseDriver
     */
    private $driver;

    /**
     * @var string[]
     */
    private $filePathList = array();

    /**
     * @var int
     */
    private $to = null;

    /**
     * MEvolution constructor.
     * @param string $settingsFilePath
     * @param string $evolutionsFolderPath
     * @param int $to
     */
    public function __construct($settingsFilePath, $evolutionsFolderPath, $to)
    {
        $this->settingsFilePath = realpath($settingsFilePath);
        $this->evolutionsFolderPath = realpath($evolutionsFolderPath);

        $this->validatePaths();

        if ($to != null) {
            $this->to = (int)$to;
        }

        $this->settings = SettingsFile::parse($this->settingsFilePath);
        $this->driver = Driver::get($this->settings);
        $this->filePathList = EvolutionFile::getList($this->evolutionsFolderPath);
    }

    /**
     * Checks if <i>$this->settingsFilePath</i> is a valid path and is a file.<br>
     * Checks if <i>$this->evolutionsFolderPath</i> is a valid path.
     *
     * @throws EvolutionFolderNotFoundException
     * @throws SettingsFileNotFoundException
     */
    private function validatePaths()
    {
        if (file_exists($this->settingsFilePath) === false || is_file($this->settingsFilePath) === false) {
            throw new SettingsFileNotFoundException($this->settingsFilePath);
        }

        if (file_exists($this->evolutionsFolderPath) === false) {
            throw new EvolutionFolderNotFoundException($this->evolutionsFolderPath);
        }
    }

    /**
     * Creates the table of the evolutions into the database.<br>
     * Inserts the new evolutions in the table.
     */
    public function init()
    {
        echo 'Creating evolution table...' . PHP_EOL;
        $this->driver->createEvolutionsTable();
        $lastEvolutionId = $this->driver->getLastEvolutionId();

        echo 'Updating evolution table...' . PHP_EOL;
        foreach ($this->filePathList as $filePath) {
            $evolution = EvolutionFile::getEvolution($filePath);

            if ($lastEvolutionId < $evolution->getId()) {
                $this->driver->insertEvolution(
                    $evolution->getId(),
                    $evolution->getUp(),
                    $evolution->getDown()
                );
                echo sprintf("\tInserted evolution %s%s.", $evolution->getId(), PHP_EOL);
            }
        }

        echo PHP_EOL . 'INIT completed without errors.' . PHP_EOL;
    }

    /**
     * Applies the evolutions.
     */
    public function apply()
    {
        echo 'Applying evolutions...' . PHP_EOL;

        if ($this->to == null) {
            $this->to = $this->driver->getLastEvolutionId();
        }

        $this->driver->executeEvolutions($this->to);

        echo PHP_EOL . 'APPLY completed without errors.' . PHP_EOL;
    }

    /**
     * Reverts the evolutions.
     */
    public function revert()
    {
        echo 'Reverting evolutions...' . PHP_EOL;

        if ($this->to == null) {
            $this->to = 0;
        }

        $this->driver->executeDevolutions($this->to);

        echo PHP_EOL . 'REVERT completed without errors.' . PHP_EOL;
    }

    /**
     * @return DatabaseDriver
     */
    public function getDriver()
    {
        return $this->driver;
    }

}
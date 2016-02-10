<?php
namespace mtoolkit\evolution\controller;

use mtoolkit\evolution\core\Controller;
use mtoolkit\evolution\drivers\DatabaseDriver;
use mtoolkit\evolution\drivers\DriverFactory;
use mtoolkit\evolution\exception\EvolutionFolderNotFoundException;
use mtoolkit\evolution\exception\SettingsFileNotFoundException;
use mtoolkit\evolution\model\evolution\EvolutionFile;
use mtoolkit\evolution\model\settings\Settings;
use mtoolkit\evolution\model\settings\SettingsFile;

abstract class EvolutionController implements Controller
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
        $this->driver = DriverFactory::get( $this->settings );
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
     * @return DatabaseDriver
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * @return \string[]
     */
    public function getFilePathList()
    {
        return $this->filePathList;
    }

    /**
     * @return int
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param int $to
     * @return EvolutionController
     */
    public function setTo( $to )
    {
        $this->to = $to;

        return $this;
    }


}
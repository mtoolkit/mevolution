<?php
namespace mtoolkit\evolution\drivers;

use mtoolkit\evolution\drivers\exceptions\GetEvolutionsException;
use mtoolkit\evolution\drivers\exceptions\InsertEvolutionException;
use mtoolkit\evolution\drivers\exceptions\UpdateExecuteDateException;
use mtoolkit\evolution\model\evolution\Evolution;
use mtoolkit\evolution\model\settings\Settings;

/**
 * Class DatabaseDriver
 * @package mtooolkit\evolution\drivers
 */
abstract class DatabaseDriver
{
    /**
     * @var Settings
     */
    private $settings;

    /**
     * DatabaseDriver constructor.
     * @param Settings $settings
     */
    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }

    /**
     * @return \PDO
     */
    public abstract function getConnection();

    /**
     * @return void
     */
    public abstract function close();

    /**
     * @return boolean
     */
    public abstract function createEvolutionsTable();

    /**
     * @param int $id Evolution id
     * @param \DateTime $executeDate
     * @return boolean
     */
    public abstract function updateExecuteDate($id, \DateTime $executeDate);

    /**
     * Sets to "<i>null</i>" the execution date of the evolution with id <i>$id</i>.
     *
     * @param int $id Evolution id
     * @throws UpdateExecuteDateException
     */
    public abstract function dropExecuteDate($id);

    /**
     * @param $from
     * @param $to
     * @return Evolution[]
     */
    public abstract function getEvolutions($from, $to);

    /**
     * Returns the id of last executed evolution.
     *
     * @return int
     * @throws GetEvolutionsException
     */
    public abstract function getLastExecutedEvolutionId();

    /**
     * @param $id
     * @param $upQuery
     * @param $downQuery
     * @throws InsertEvolutionException
     */
    public abstract function insertEvolution($id, $upQuery, $downQuery);

    /**
     * Returns the ID of the evolution in table.
     *
     * @return int
     */
    public abstract function getLastEvolutionId();

    public abstract function clean();

    /**
     * @param int $to
     */
    public abstract function executeEvolutions($to = null);

    /**
     * @param int $to
     */
    public abstract function executeDevolutions($to = null);

    /**
     * @return Settings
     */
    protected function getSettings()
    {
        return $this->settings;
    }
}
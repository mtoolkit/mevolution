<?php
namespace mtoolkit\evolution\drivers;

use mtoolkit\evolution\drivers\exceptions\CleanEvolutionsTableException;
use mtoolkit\evolution\drivers\exceptions\CreateEvolutionsTableException;
use mtoolkit\evolution\drivers\exceptions\GetEvolutionsException;
use mtoolkit\evolution\drivers\exceptions\InsertEvolutionException;
use mtoolkit\evolution\drivers\exceptions\DropEvolutionException;
use mtoolkit\evolution\model\error\ErrorNumber;
use mtoolkit\evolution\model\evolution\Evolution;
use mtoolkit\evolution\model\settings\Settings;

/**
 * Class MySQLDriver
 *
 * @package mtooolkit\evolution\drivers
 */
class MySQLDriver extends DatabaseDriver
{
    private $mysql = null;

    /**
     * MySQLDriver constructor.
     *
     * @param Settings $settings
     */
    public function __construct(Settings $settings)
    {
        parent::__construct($settings);
    }

    public function close()
    {
        $this->getConnection()->close();
        $this->mysql = null;
    }

    /**
     * @return \mysqli
     * @throws \Exception
     */
    public function getConnection()
    {
        if ($this->mysql == null)
        {
            $this->mysql = new \mysqli($this->getSettings()->getHost(), $this->getSettings()->getUsername(), $this->getSettings()->getPassword(), $this->getSettings()->getDbName());
        }

        try
        {
            return $this->mysql;
        } catch (\Exception $ex)
        {
            throw new \Exception("Impossible to connect to the database.");
        }
    }

    /**
     * @throws CreateEvolutionsTableException
     */
    public function createEvolutionsTable()
    {
        $connection = $this->getConnection();
        $sql = 'CREATE TABLE IF NOT EXISTS mt_evolutions (
			id INT NOT NULL PRIMARY KEY,
			up TEXT NOT NULL,
			down TEXT,
			inserted DATETIME NOT NULL,
			executed DATETIME
		)';

        $result = $connection->query($sql);

        if ($result === false)
        {
            throw new CreateEvolutionsTableException('mt_evolutions');
        }

        // $result->close();
    }

    /**
     * @param $id
     * @param $upQuery
     * @param $downQuery
     * @throws InsertEvolutionException
     * @throws \Exception
     */
    public function insertEvolution($id, $upQuery, $downQuery)
    {
        $connection = $this->getConnection();

        $sql = "INSERT INTO `mt_evolutions` (`id`, `up`, `down`, `inserted`)
        VALUES (?, ?, ?, NOW())
        ON DUPLICATE KEY UPDATE `up` = ?, `down`=?;";

        $stmt = $connection->prepare($sql);

        if ($stmt === false)
        {
            $errorMessage = sprintf("%s: %s", ErrorNumber::EN_001, $connection->error);
            throw new \Exception($errorMessage, $connection->errno);
        }

        $stmt->bind_param('issss', $id, $upQuery, $downQuery, $upQuery, $downQuery);
        $result = $stmt->execute();
        $stmt->free_result();
        $stmt->close();

        if ($result === false)
        {
            throw new InsertEvolutionException();
        }
    }

    /**
     * @param $from
     * @param $to
     * @return \mtoolkit\evolution\model\evolution\Evolution[]
     * @throws GetEvolutionsException
     * @throws \Exception
     */
    public function getEvolutions($from, $to)
    {
        $connection = $this->getConnection();

        $stmt = $connection->prepare("SELECT id, up, down, inserted, executed
            FROM mt_evolutions
            WHERE ( id>=? OR ? IS NULL )
                AND ( id<=? OR ? IS NULL )
            ORDER BY id ASC;");

        if ($stmt === false)
        {
            $errorMessage = sprintf("%s: %s", ErrorNumber::EN_002, $connection->error);
            throw new \Exception($errorMessage, $connection->errno);
        }

        $stmt->bind_param('iiii', $from, $from, $to, $to);
        $result = $stmt->execute();

        if ($result === false)
        {
            throw new GetEvolutionsException();
        }

        $toReturn = array();
        $stmt->bind_result($id, $up, $down, $inserted, $executed);
        while ($stmt->fetch())
        {
            $evolution = new Evolution();
            $evolution->setId($id)
                ->setUp($up)
                ->setDown($down);

            if ($executed != null)
            {
                $evolution->setExecuted(new \DateTime($executed));
            }
            if ($inserted != null)
            {
                $evolution->setInserted(new \DateTime($inserted));
            }

            $toReturn[] = $evolution;
        }

        $stmt->free_result();
        $stmt->close();

        return $toReturn;
    }

    /**
     * @param int $id Evolution id
     * @param \DateTime $executeDate
     * @throws DropEvolutionException
     * @return void
     */
    public function updateExecuteDate($id, \DateTime $executeDate)
    {
        $connection = $this->getConnection();

        $stmt = $connection->prepare("UPDATE mt_evolutions SET executed=? WHERE id=?;");
        $stmt->bind_param('si', $executeDate->format('Y-m-d H:i:s'), $id);
        $result = $stmt->execute();

        $stmt->free_result();
        $stmt->close();

        if ($result === false)
        {
            throw new DropEvolutionException();
        }
    }

    /**
     * @param int $id Evolution id
     * @throws DropEvolutionException
     */
    public function dropEvolution( $id )
    {
        $connection = $this->getConnection();

        $stmt = $connection->prepare( "DELETE FROM mt_evolutions WHERE id=?;" );
        $stmt->bind_param('i', $id);
        $result = $stmt->execute();

        $stmt->free_result();
        $stmt->close();

        if ($result === false)
        {
            throw new DropEvolutionException();
        }
    }

    /**
     * Returns the id of lastest executed evolution.
     *
     * @return int
     * @throws GetEvolutionsException
     */
    public function getLastExecutedEvolutionId()
    {
        $connection = $this->getConnection();

        $stmt = $connection->prepare("SELECT MAX(id) AS id
            FROM mt_evolutions
            WHERE executed IS NOT NULL");
        $result = $stmt->execute();

        if ($result === false)
        {
            throw new GetEvolutionsException();
        }

        $toReturn = 0;
        $stmt->bind_result($id);
        while ($stmt->fetch())
        {
            if ($id != null)
            {
                $toReturn = (int)$id;
            }
        }

        $stmt->free_result();
        $stmt->close();

        return $toReturn;
    }

    /**
     * Returns the ID of the evolution in table.
     *
     * @return int
     * @throws GetEvolutionsException
     */
    public function getLastEvolutionId()
    {
        $connection = $this->getConnection();

        $stmt = $connection->prepare("SELECT MAX(id) AS id
            FROM mt_evolutions");
        $result = $stmt->execute();

        if ($result === false)
        {
            throw new GetEvolutionsException();
        }

        $toReturn = 0;
        $stmt->bind_result($id);
        while ($stmt->fetch())
        {
            $toReturn = (int)$id;
        }

        $stmt->free_result();
        $stmt->close();

        return $toReturn;
    }

    /**
     * <ul>
     *      <li>Retrieves the id of last executed evolution {@link #getLastExecutedEvolutionId}</li>
     *      <li>Retrieves the list of the evolution to execute {@link #getEvolutions}</li>
     *      <li>Begins the transaction</li>
     *      <li>Runs each evolution and updates its execution date {@link #updateExecuteDate}</li>
     *      <li>If there is any errors, commits the transaction, otherwise roll back</li>
     * </ul>
     *
     * @param int $to Optional
     * @throws \Exception
     */
    public function executeEvolutions($to = null)
    {
        $connection = $this->getConnection();

        try
        {
            $from = $this->getLastExecutedEvolutionId();
            /* @var Evolution[] $evolutions */
            $evolutions = $this->getEvolutions($from, $to);
            $connection->autocommit(false);
            $connection->begin_transaction();

            foreach ($evolutions as $evolution)
            {
                if ($evolution->getExecuted() != null)
                {
                    continue;
                }

                echo sprintf("\tApplying evolution %s...\n", $evolution->getId());
                $this->execute($evolution->getUp());
                $this->updateExecuteDate($evolution->getId(), new \DateTime());
                echo sprintf("\tEvolution %s applied without error.\n", $evolution->getId());
            }

            $connection->commit();
        } catch (\Exception $ex)
        {
            $connection->rollBack();
            throw $ex;
        }
    }

    /**
     * @param int $to Optional
     * @throws \Exception
     */
    public function executeDevolutions($to = null)
    {
        $connection = $this->getConnection();

        try
        {
            $from = $this->getLastExecutedEvolutionId();
            $connection->autocommit(false);
            $connection->begin_transaction();

            /* @var Evolution[] $evolutions */
            $evolutions = array_reverse($this->getEvolutions($to, $from));

            foreach ($evolutions as $evolution)
            {
                echo sprintf("\tApplying devolution %s...\n", $evolution->getId());
                $this->execute($evolution->getDown());
                $this->dropEvolution($evolution->getId());
                echo sprintf("\tDevolution %s applied without error.\n", $evolution->getId());
            }

            $connection->commit();
        } catch (\Exception $ex)
        {
            $connection->rollBack();
            throw $ex;
        }
    }

    /**
     * @param $sql
     * @throws \Exception
     */
    public function execute($sql)
    {
        $connection = $this->getConnection();

        if ($connection->multi_query($sql))
        {
            do
            {
                /* store first result set */
                if ($result = $connection->store_result())
                {
                    while ($row = $result->fetch_row())
                    {
                        //printf("%s\n", $row[0]);
                    }
                    $result->free();
                }
                if ($connection->more_results())
                {
                    //printf("-----------------\n");
                }
            } while ($connection->next_result());
        }
        else
        {
            throw new \Exception(sprintf('Error executing sql command: %s', $sql));
        }
    }

    public function clean()
    {
        $connection = $this->getConnection();
        $sql = 'DELETE FROM mt_evolutions';

        $result = $connection->query($sql);

        if ($result === false)
        {
            throw new CleanEvolutionsTableException('mt_evolutions');
        }
    }
}
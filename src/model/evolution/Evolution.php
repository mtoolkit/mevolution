<?php
namespace mtoolkit\evolution\model\evolution;

/**
 * Class Evolution
 * @package mtooolkit\evolution\model
 */
class Evolution
{
    /**
     * @var number
     */
    private $id;

    /**
     * @var string
     */
    private $up;

    /**
     * @var string
     */
    private $down;

    /**
     * @var \DateTime|null
     */
    private $inserted=null;

    /**
     * @var \DateTime|null
     */
    private $executed=null;

    /**
     * @return number
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param number $id
     * @return Evolution
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getUp()
    {
        return $this->up;
    }

    /**
     * @param string $up
     * @return Evolution
     */
    public function setUp($up)
    {
        $this->up = $up;
        return $this;
    }

    /**
     * @return string
     */
    public function getDown()
    {
        return $this->down;
    }

    /**
     * @param string $down
     * @return Evolution
     */
    public function setDown($down)
    {
        $this->down = $down;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getInserted()
    {
        return $this->inserted;
    }

    /**
     * @param \DateTime|null $inserted
     * @return Evolution
     */
    public function setInserted(\DateTime $inserted)
    {
        $this->inserted = $inserted;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getExecuted()
    {
        return $this->executed;
    }

    /**
     * @param \DateTime|null $executed
     * @return Evolution
     */
    public function setExecuted(\DateTime $executed)
    {
        $this->executed = $executed;
        return $this;
    }
}

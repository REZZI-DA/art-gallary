<?php
class Genre
{
    private $id;
    private $name;
    private $Era;
    private $Description;
    private $Link;

    /**
     * @param $id
     * @param $name
     * @param $Era
     * @param $Description
     * @param $Link
     */
    public function __construct($id, $name, $Era, $Description, $Link)
    {
        $this->id = $id;
        $this->name = $name;
        $this->Era = $Era;
        $this->Description = $Description;
        $this->Link = $Link;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getEra()
    {
        return $this->Era;
    }

    /**
     * @param mixed $Era
     */
    public function setEra($Era): void
    {
        $this->Era = $Era;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->Description;
    }

    /**
     * @param mixed $Description
     */
    public function setDescription($Description): void
    {
        $this->Description = $Description;
    }

    /**
     * @return mixed
     */
    public function getLink()
    {
        return $this->Link;
    }

    /**
     * @param mixed $Link
     */
    public function setLink($Link): void
    {
        $this->Link = $Link;
    }




}

<?php

class FolderEntity extends \stdClass
{
    private $date;
    private $path;
    private $dateOnly;
    private $areaRootDate;

    public function getDate()
    {
        return $this->date;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getDateOnly()
    {
        return $this->dateOnly;
    }

    public function getAreaRootDate()
    {
        return $this->areaRootDate;
    }


    public function __construct($dateOnly, $date, $path)
    {
        $this->dateOnly = $dateOnly;
        $this->date = $date;
        $this->path = $path . DIRECTORY_SEPARATOR . $date;
        $this->areaRootDate = substr($this->path, 0, strpos($this->path, $dateOnly) + strlen($dateOnly));
    }
}
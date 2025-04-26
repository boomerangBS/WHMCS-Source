<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS;

// Decoded file for php version 72.
class TableQuery
{
    protected $recordOffset = 0;
    protected $recordLimit = 25;
    protected $data = [];
    public function getData()
    {
        return $this->data;
    }
    public function getOne()
    {
        return isset($this->data[0]) ? $this->data[0] : NULL;
    }
    public function setRecordLimit($limit)
    {
        $this->recordLimit = $limit;
        return $this;
    }
    public function getRecordLimit()
    {
        return $this->recordLimit;
    }
    public function getRecordOffset()
    {
        $page = $this->getPageObj()->getPage();
        $offset = ($page - 1) * $this->getRecordLimit();
        return $offset;
    }
    public function getQueryLimit()
    {
        return $this->getRecordOffset() . "," . $this->getRecordLimit();
    }
    public function setData($data = [])
    {
        if(!is_array($data) && !$data instanceof \Illuminate\Support\Collection) {
            throw new \InvalidArgumentException("Dataset must be an array");
        }
        $this->data = $data;
        return $this;
    }
}

?>
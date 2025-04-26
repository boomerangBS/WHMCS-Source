<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS;

// Decoded file for php version 72.
abstract class TableModel extends TableQuery
{
    protected $pageObj;
    protected $queryObj;
    public function __construct(Pagination $obj = NULL)
    {
        $this->pageObj = $obj;
        $numrecords = Config\Setting::getValue("NumRecordstoDisplay");
        $this->setRecordLimit($numrecords);
        return $this;
    }
    public abstract function _execute(array $implementationData);
    public function setPageObj(Pagination $pageObj)
    {
        $this->pageObj = $pageObj;
    }
    public function getPageObj()
    {
        return $this->pageObj;
    }
    public function execute(array $criteria = [])
    {
        $results = $this->_execute($criteria);
        $this->getPageObj()->setData($results);
        return $this;
    }
}

?>
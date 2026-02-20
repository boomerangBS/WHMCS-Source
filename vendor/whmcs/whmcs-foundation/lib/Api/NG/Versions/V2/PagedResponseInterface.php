<?php

namespace WHMCS\Api\NG\Versions\V2;

interface PagedResponseInterface
{
    public function hasPageInformation();
    public function getPageNumber() : int;
    public function setPageNumber($pageNumber) : void;
    public function getPageCount() : int;
    public function setPageCount($pageCount) : void;
}

?>
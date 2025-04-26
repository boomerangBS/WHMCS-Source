<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Api\NG\Versions\V2;

trait PagedResponseTrait
{
    private $pageNumber;
    private $pageCount;
    private $defaultPageSize = 50;
    public function paginateData($data, \WHMCS\Http\Message\ServerRequest $request, int $pageSize = NULL)
    {
        if(is_null($pageSize)) {
            $pageSize = $this->defaultPageSize;
        } elseif($pageSize < 1) {
            $pageSize = 1;
        } elseif(50 < $pageSize) {
            $pageSize = 50;
        }
        $pageCount = (int) ceil(count($data) / $pageSize);
        $pageNumber = $request->get("page", 1);
        if($pageNumber < 1) {
            $pageNumber = 1;
        } elseif($pageCount < $pageNumber) {
            $pageNumber = $pageCount;
        }
        $this->setPageNumber($pageNumber);
        $this->setPageCount($pageCount);
        $itemsToSkip = ($pageNumber - 1) * $pageSize;
        if($data instanceof \Illuminate\Support\Collection) {
            $dataPage = $data->skip($itemsToSkip)->take($pageSize)->values();
        } elseif(is_array($data)) {
            $dataPage = array_slice($data, $itemsToSkip, $pageSize);
        }
        return $dataPage;
    }
    public function hasPageInformation()
    {
        return !is_null($this->pageNumber) && !is_null($this->pageCount);
    }
    public function getPageNumber() : int
    {
        if(is_null($this->pageNumber)) {
            throw new \WHMCS\Exception\Api\NG\ApiNgException("Page number must be set");
        }
        return $this->pageNumber;
    }
    public function setPageNumber($pageNumber) : void
    {
        $this->pageNumber = $pageNumber;
    }
    public function getPageCount() : int
    {
        if(is_null($this->pageCount)) {
            throw new \WHMCS\Exception\Api\NG\ApiNgException("You must set a page count.");
        }
        return $this->pageCount;
    }
    public function setPageCount($pageCount) : void
    {
        $this->pageCount = $pageCount;
    }
}

?>
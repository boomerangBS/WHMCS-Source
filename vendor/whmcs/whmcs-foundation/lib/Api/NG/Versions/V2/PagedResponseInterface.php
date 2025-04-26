<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
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
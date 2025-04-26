<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Http;

trait DataTrait
{
    protected $rawData = [];
    public function getRawData()
    {
        return $this->rawData;
    }
    public function setRawData($rawData)
    {
        $this->rawData = $rawData;
        return $this;
    }
}

?>
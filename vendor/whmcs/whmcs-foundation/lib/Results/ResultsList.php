<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Results;

class ResultsList extends \ArrayObject
{
    public function toArray()
    {
        $result = [];
        foreach ($this->getArrayCopy() as $key => $data) {
            $result[$key] = $data->toArray();
        }
        return $result;
    }
}

?>
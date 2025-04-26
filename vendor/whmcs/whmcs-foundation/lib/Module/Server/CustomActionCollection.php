<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Server;

class CustomActionCollection extends \Illuminate\Support\Collection
{
    public function add($item) : \self
    {
        if(!$item instanceof CustomAction) {
            throw new \InvalidArgumentException("The provided item must be an instance of the CustomAction class.");
        }
        parent::add($item);
        return $this;
    }
}

?>
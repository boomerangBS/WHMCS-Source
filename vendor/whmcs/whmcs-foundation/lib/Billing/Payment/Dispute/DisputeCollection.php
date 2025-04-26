<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Billing\Payment\Dispute;

class DisputeCollection extends \Illuminate\Support\Collection
{
    public static function factoryFromItems(\WHMCS\Billing\Payment\DisputeInterface ...$dispute) : DisputeCollection
    {
        return new static($dispute);
    }
    public static function factoryFromArray($disputes) : DisputeCollection
    {
        $disputeObjects = array_map(function (array $disputeArray) {
            return \WHMCS\Billing\Payment\Dispute::factoryFromArray($disputeArray);
        }, $disputes);
        return static::factoryFromItems(...$disputeObjects);
    }
    public function addDispute(\WHMCS\Billing\Payment\DisputeInterface $dispute) : \self
    {
        $this->add($dispute);
        return $this;
    }
}

?>
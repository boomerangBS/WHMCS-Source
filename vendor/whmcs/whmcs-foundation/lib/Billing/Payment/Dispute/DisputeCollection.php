<?php

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
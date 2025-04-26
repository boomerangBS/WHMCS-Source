<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Payment\Event\Traits;

trait DateAwareTrait
{
    private $date;
    public function date() : \WHMCS\Carbon
    {
        return $this->date;
    }
    public function setDate(\WHMCS\Carbon $date) : \self
    {
        $this->date = $date;
        return $this;
    }
    protected function hasDate()
    {
        return !is_null($this->date);
    }
    protected function assertDate() : \self
    {
        if(!$this->hasDate()) {
            throw \WHMCS\Payment\Exception\MissingRequirement::ofImplementor("date", self::class);
        }
        return $this;
    }
}

?>
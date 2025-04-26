<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace M101Domain;

class Domain
{
    public $name;
    public $status = [];
    public $registrant;
    public $contacts = [];
    public $ns = [];
    public $cr_date;
    public $up_date;
    public $ex_date;
    public $key;
    protected $lockedStatuses = ["clientTransferProhibited", "clientHold", "serverTransferProhibited", "serverHold"];
    public function isLocked()
    {
        foreach ($this->status as $status) {
            if(in_array($status, $this->lockedStatuses)) {
                return true;
            }
        }
        return false;
    }
}

?>
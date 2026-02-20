<?php

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
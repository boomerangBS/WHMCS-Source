<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Billing\Quote;

class Item extends \WHMCS\Model\AbstractModel
{
    protected $table = "tblquoteitems";
    protected $booleans = ["taxable"];
    protected $columnMap = ["isTaxable" => "taxable"];
    public function quote()
    {
        return $this->belongsTo("WHMCS\\Billing\\Quote", "quoteid", "id", "quote");
    }
    public function getTotal()
    {
        return (double) ($this->quantity * $this->unitPrice) * (1 - $this->discount / 100);
    }
}

?>
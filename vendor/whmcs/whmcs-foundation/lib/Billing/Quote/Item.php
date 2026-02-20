<?php

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
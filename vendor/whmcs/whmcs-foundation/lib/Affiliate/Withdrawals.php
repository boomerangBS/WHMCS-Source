<?php

namespace WHMCS\Affiliate;

class Withdrawals extends \WHMCS\Model\AbstractModel
{
    protected $table = "tblaffiliateswithdrawals";
    protected $columnMap = ["affiliateId" => "affiliateid"];
    protected $dates = ["date"];
    public function affiliate() : \Illuminate\Database\Eloquent\Relations\Relation
    {
        return $this->belongsTo("WHMCS\\User\\Client\\Affiliate", "affiliateid", "id", "affiliate");
    }
}

?>
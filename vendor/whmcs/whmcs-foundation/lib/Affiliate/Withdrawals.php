<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
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
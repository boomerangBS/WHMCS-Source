<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Affiliate;

class Accounts extends \WHMCS\Model\AbstractModel
{
    protected $table = "tblaffiliatesaccounts";
    protected $columnMap = ["affiliateId" => "affiliateid", "serviceId" => "relid", "lastPaid" => "lastpaid"];
    protected $dates = ["lastpaid"];
    public function affiliate() : \Illuminate\Database\Eloquent\Relations\Relation
    {
        return $this->belongsTo("WHMCS\\User\\Client\\Affiliate", "affiliateid", "id", "affiliate");
    }
    public function history() : \Illuminate\Database\Eloquent\Relations\Relation
    {
        return $this->hasMany("WHMCS\\Affiliate\\History", "affaccid");
    }
    public function pending() : \Illuminate\Database\Eloquent\Relations\Relation
    {
        return $this->hasMany("WHMCS\\Affiliate\\Pending", "affaccid");
    }
    public function service() : \Illuminate\Database\Eloquent\Relations\Relation
    {
        return $this->belongsTo("WHMCS\\Service\\Service", "relid", "id", "service");
    }
}

?>
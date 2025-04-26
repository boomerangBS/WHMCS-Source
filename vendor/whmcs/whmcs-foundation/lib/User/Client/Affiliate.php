<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\User\Client;

class Affiliate extends \WHMCS\Model\AbstractModel
{
    protected $table = "tblaffiliates";
    protected $columnMap = ["visitorCount" => "visitors", "commissionType" => "paytype", "paymentAmount" => "payamount", "isPaidOneTimeCommission" => "onetime", "amountWithdrawn" => "withdrawn"];
    protected $dates = ["date"];
    protected $appends = ["pendingCommissionAmount"];
    public static function boot()
    {
        parent::boot();
        static::observe("WHMCS\\User\\Observers\\AffiliateObserver");
    }
    public function accounts() : \Illuminate\Database\Eloquent\Relations\Relation
    {
        return $this->hasMany("WHMCS\\Affiliate\\Accounts", "affiliateid");
    }
    public function client()
    {
        return $this->belongsTo("WHMCS\\User\\Client", "clientid", "id", "client");
    }
    public function history() : \Illuminate\Database\Eloquent\Relations\Relation
    {
        return $this->hasMany("WHMCS\\Affiliate\\History", "affiliateid");
    }
    public function hits() : \Illuminate\Database\Eloquent\Relations\Relation
    {
        return $this->hasMany("WHMCS\\Affiliate\\Hit");
    }
    public function referrers() : \Illuminate\Database\Eloquent\Relations\Relation
    {
        return $this->hasMany("WHMCS\\Affiliate\\Referrer");
    }
    public function withdrawals() : \Illuminate\Database\Eloquent\Relations\Relation
    {
        return $this->hasMany("WHMCS\\Affiliate\\Withdrawals", "affiliateid");
    }
    public function pending() : \Illuminate\Database\Eloquent\Relations\Relation
    {
        return $this->hasManyThrough("WHMCS\\Affiliate\\Pending", "WHMCS\\Affiliate\\Accounts", "affiliateid", "affaccid");
    }
    public function getReferralLink()
    {
        return \App::getSystemURL() . "aff.php?aff=" . $this->id;
    }
    public function getAdminLink()
    {
        return \App::get_admin_folder_name() . "/affiliates.php?action=edit&id=" . $this->id;
    }
    public function getFullAdminUrl()
    {
        return \App::getSystemURL() . $this->getAdminLink();
    }
    public function getPendingCommissionAmountAttribute()
    {
        return $this->pending()->sum("amount");
    }
}

?>
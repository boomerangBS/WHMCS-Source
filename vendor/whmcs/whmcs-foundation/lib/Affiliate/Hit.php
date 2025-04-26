<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Affiliate;

class Hit extends \WHMCS\Model\AbstractModel
{
    protected $table = "tblaffiliates_hits";
    public $timestamps = false;
    public $dates = ["created_at"];
    protected $fillable = ["affiliate_id", "referrer_id", "created_at"];
    public function referrer()
    {
        return $this->belongsTo("WHMCS\\Affiliate\\Referrer", "referrer_id", "id", "referrer");
    }
    public function affiliate() : \Illuminate\Database\Eloquent\Relations\Relation
    {
        return $this->belongsTo("WHMCS\\User\\Client\\Affiliate", "affiliate_id", "id", "affiliate");
    }
}

?>
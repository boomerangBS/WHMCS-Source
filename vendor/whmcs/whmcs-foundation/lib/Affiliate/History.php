<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Affiliate;

class History extends \WHMCS\Model\AbstractModel
{
    protected $table = "tblaffiliateshistory";
    protected $columnMap = ["affiliateId" => "affiliateid", "affiliateAccountId" => "affaccid", "invoiceId" => "invoice_id"];
    protected $dates = ["date"];
    protected $fillable = ["affiliateid", "date", "invoice_id", "amount", "description"];
    public function affiliate() : \Illuminate\Database\Eloquent\Relations\Relation
    {
        return $this->belongsTo("WHMCS\\User\\Client\\Affiliate", "affiliateid", "id", "affiliate");
    }
    public function account() : \Illuminate\Database\Eloquent\Relations\Relation
    {
        return $this->belongsTo("WHMCS\\Affiliate\\Accounts", "affaccid", "id", "account");
    }
    public function invoice() : \Illuminate\Database\Eloquent\Relations\Relation
    {
        return $this->hasOne("WHMCS\\Billing\\Invoice", "id", "invoice_id");
    }
    public function reverse(int $invoiceId = 0)
    {
        $newRecord = $this->replicate();
        $newRecord->amount *= -1;
        $newRecord->description = "Commission reversal due to refund of invoice payment.";
        if($invoiceId && !$this->invoiceId) {
            $newRecord->invoiceId = $invoiceId;
            $this->invoiceId = $invoiceId;
            $this->save();
        }
        $newRecord->save();
        $this->affiliate->balance -= $this->amount;
        $this->affiliate->save();
    }
}

?>
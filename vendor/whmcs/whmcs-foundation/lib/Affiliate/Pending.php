<?php

namespace WHMCS\Affiliate;

class Pending extends \WHMCS\Model\AbstractModel
{
    protected $table = "tblaffiliatespending";
    protected $columnMap = ["affiliateAccountId" => "affaccid", "invoiceId" => "invoice_id", "clearingDate" => "clearingdate"];
    protected $dates = ["clearingDate"];
    protected $fillable = ["invoice_id", "amount", "clearingdate"];
    public function account() : \Illuminate\Database\Eloquent\Relations\Relation
    {
        return $this->belongsTo("WHMCS\\Affiliate\\Accounts", "affaccid", "id", "account");
    }
    public function invoice() : \Illuminate\Database\Eloquent\Relations\Relation
    {
        return $this->hasOne("WHMCS\\Billing\\Invoice", "id", "invoice_id");
    }
}

?>
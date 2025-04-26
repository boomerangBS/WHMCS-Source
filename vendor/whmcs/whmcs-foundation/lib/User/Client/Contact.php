<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\User\Client;

class Contact extends \WHMCS\Model\AbstractModel implements \WHMCS\User\Contracts\ContactInterface
{
    use \WHMCS\User\Traits\EmailPreferences;
    protected $table = "tblcontacts";
    protected $columnMap = ["clientId" => "userid", "isSubAccount" => "subaccount", "passwordHash" => "password", "receivesDomainEmails" => "domainemails", "receivesGeneralEmails" => "generalemails", "receivesInvoiceEmails" => "invoiceemails", "receivesProductEmails" => "productemails", "receivesSupportEmails" => "supportemails", "receivesAffiliateEmails" => "affiliateemails", "passwordResetKey" => "pwresetkey", "passwordResetKeyExpiryDate" => "pwresetexpiry"];
    protected $dates = ["passwordResetKeyExpiryDate"];
    protected $booleans = ["isSubAccount", "receivesDomainEmails", "receivesGeneralEmails", "receivesInvoiceEmails", "receivesProductEmails", "receivesSupportEmails", "receivesAffiliateEmails"];
    protected $commaSeparated = ["permissions"];
    protected $appends = ["fullName", "countryName"];
    protected $hidden = ["password", "pwresetkey", "pwresetexpiry"];
    public function client()
    {
        return $this->belongsTo("WHMCS\\User\\Client", "userid", "id", "client");
    }
    public function remoteAccountLinks()
    {
        return $this->hasMany("WHMCS\\Authentication\\Remote\\AccountLink", "contact_id");
    }
    public function orders()
    {
        return $this->hasMany("WHMCS\\Order\\Order", "id", "orderid");
    }
    public function scopeLegacySubaccount($query)
    {
        return $query->where("subaccount", 1);
    }
    public function getFullNameAttribute()
    {
        return $this->firstname . " " . $this->lastname;
    }
    public function getCountryNameAttribute()
    {
        if(is_null($countries)) {
            $countries = new \WHMCS\Utility\Country();
        }
        return $countries->getName($this->country);
    }
    public function updateLastLogin(\WHMCS\Carbon $time = NULL, $ip = NULL, $host = NULL)
    {
        return $this->client->updateLastLogin($time, $ip, $host);
    }
    public function getLanguageAttribute()
    {
        return $this->client->language;
    }
    public function getTwoFactorAuthModuleAttribute()
    {
        return "";
    }
    public function getPhoneNumberFormattedAttribute()
    {
        $phoneUtil = new \WHMCS\Utility\Phone($this->phonenumber, $this->country);
        return $phoneUtil->getTelephoneNumber();
    }
    public function tickets()
    {
        return $this->hasMany("WHMCS\\Support\\Ticket", "contactid");
    }
}

?>
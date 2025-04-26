<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Domain;

class Status
{
    protected $statusValues;
    const PENDING = "Pending";
    const PENDING_REGISTRATION = "Pending Registration";
    const PENDING_TRANSFER = "Pending Transfer";
    const ACTIVE = "Active";
    const GRACE = "Grace";
    const REDEMPTION = "Redemption";
    const EXPIRED = "Expired";
    const TRANSFERRED_AWAY = "Transferred Away";
    const CANCELLED = "Cancelled";
    const FRAUD = "Fraud";
    public function all()
    {
        return $this->statusValues;
    }
    public function allWithTranslations()
    {
        $statuses = [];
        foreach ($this->statusValues as $status) {
            $statuses[$status] = $this->translate($status);
        }
        return $statuses;
    }
    public static function translate($status)
    {
        $status = strtolower(str_replace(" ", "", $status));
        if(defined("ADMINAREA")) {
            return \AdminLang::trans("status." . $status);
        }
        return \Lang::trans("status." . $status);
    }
    public function translatedDropdownOptions(array $selectedStatus = NULL)
    {
        $options = "";
        foreach ($this->allWithTranslations() as $dbValue => $translation) {
            $selected = is_array($selectedStatus) && in_array($dbValue, $selectedStatus) ? " selected=\"selected\"" : "";
            $options .= "<option value=\"" . $dbValue . "\"" . $selected . ">" . $translation . "</option>";
        }
        return $options;
    }
}

?>
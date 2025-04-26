<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Marketing;

class Consent extends \WHMCS\Model\AbstractModel
{
    protected $table = "tblmarketing_consent";
    protected $booleans = ["optIn", "admin"];
    public function client()
    {
        return $this->belongsTo("WHMCS\\User\\Client", "userid", "id", "client");
    }
    public static function logOptIn($userId, $userIp = "")
    {
        if(empty($userIp)) {
            $userIp = \App::getRemoteIp();
        }
        $isAdmin = 0 < \WHMCS\Session::get("adminid");
        $consent = new self();
        $consent->userid = $userId;
        $consent->optIn = true;
        $consent->admin = $isAdmin;
        $consent->ipAddress = $userIp;
        return $consent->save();
    }
    public static function logOptOut($userId, $userIp = "")
    {
        if(empty($userIp)) {
            $userIp = \App::getRemoteIp();
        }
        $isAdmin = 0 < \WHMCS\Session::get("adminid");
        $consent = new self();
        $consent->userid = $userId;
        $consent->optIn = false;
        $consent->admin = $isAdmin;
        $consent->ipAddress = $userIp;
        return $consent->save();
    }
}

?>
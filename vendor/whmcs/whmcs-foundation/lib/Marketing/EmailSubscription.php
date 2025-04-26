<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Marketing;

class EmailSubscription
{
    const ACTION_OPTIN = "optin";
    const ACTION_OPTOUT = "optout";
    public static function isUsingOptInField()
    {
        return \WHMCS\Config\Setting::getValue("MarketingEmailConvert");
    }
    public function generateOptInUrl(int $userId, string $email, $link)
    {
        return $this->generateOptInOutUrl(self::ACTION_OPTIN, $userId, $email, $link);
    }
    public function generateOptOutUrl(int $userId, string $email, $link)
    {
        return $this->generateOptInOutUrl(self::ACTION_OPTOUT, $userId, $email, $link);
    }
    protected function generateOptInOutUrl(string $action, int $userId, string $email, $link)
    {
        $url = fqdnRoutePath("subscription-manage");
        if(strpos($url, "?") === false) {
            $url .= "?";
        } else {
            $url .= "&";
        }
        $fullUrl = $url . "action=" . $action . "&email=" . urlencode($email) . "&key=" . $this->generateKey($userId, $email, $action);
        if($link) {
            return "<a href=\"" . $fullUrl . "\">" . $fullUrl . "</a>";
        }
        return $fullUrl;
    }
    public function generateKey($userId, $email, $action)
    {
        if($action == self::ACTION_OPTOUT) {
            $action = "";
        } else {
            $action = self::ACTION_OPTIN;
        }
        return sha1($action . $email . $userId . \App::get_hash());
    }
    public function validateKey(\WHMCS\User\Client $client, $action, $key)
    {
        if($key != $this->generateKey($client->id, $client->email, $action)) {
            throw new \WHMCS\Exception\Validation\InvalidValue("Invalid key");
        }
    }
}

?>
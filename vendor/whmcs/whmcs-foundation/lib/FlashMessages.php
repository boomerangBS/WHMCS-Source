<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS;

// Decoded file for php version 72.
class FlashMessages
{
    public static function add($message, $status = "success")
    {
        $flashMessages = Session::get("flash");
        if(!is_array($flashMessages)) {
            $flashMessages = [];
        }
        if(defined("ROUTE_CONVERTED_LEGACY_ENDPOINT")) {
            $filename = Utility\Environment\WebHelper::getBaseUrl() . "/index.php";
        } elseif(defined("ROUTE_REDIRECT_TO_LEGACY")) {
            $filename = ROUTE_REDIRECT_TO_LEGACY;
        } else {
            $filename = \App::getPhpSelf();
        }
        $flashMessages[$filename] = ["type" => $status, "text" => $message];
        Session::set("flash", $flashMessages);
    }
    public static function get()
    {
        $phpSelf = \App::getPhpSelf();
        $flashMessages = Session::get("flash");
        if(isset($flashMessages[$phpSelf])) {
            $flashMessage = $flashMessages[$phpSelf];
            unset($flashMessages[$phpSelf]);
            Session::set("flash", $flashMessages);
            return $flashMessage;
        }
        return false;
    }
}

?>
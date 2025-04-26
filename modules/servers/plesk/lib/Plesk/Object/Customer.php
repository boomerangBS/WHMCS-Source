<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class Plesk_Object_Customer
{
    const STATUS_ACTIVE = 0;
    const STATUS_SUSPENDED_BY_ADMIN = 16;
    const STATUS_SUSPENDED_BY_RESELLER = 32;
    const TYPE_CLIENT = "hostingaccount";
    const TYPE_RESELLER = "reselleraccount";
    const EXTERNAL_ID_PREFIX = "whmcs_plesk_";
    public static function getCustomerExternalId($params)
    {
        if(isset($params["clientsdetails"]["panelExternalId"]) && "" != $params["clientsdetails"]["panelExternalId"]) {
            return $params["clientsdetails"]["panelExternalId"];
        }
        return $params["clientsdetails"]["uuid"];
    }
}

?>
<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Support\Ticket;

interface RequestorTypes
{
    const ADMIN = "Operator";
    const OWNER = "Owner";
    const USER = "Authorized User";
    const REGISTERED_USER = "Registered User";
    const LEGACY_SUBACCOUNT = "Sub-account";
    const GUEST = "Guest";
}

?>
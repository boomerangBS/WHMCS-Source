<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Order;

abstract class OrderPurchaseSource
{
    const ADMIN_MASQUERADING_AS_CLIENT = 1;
    const CLIENT = 2;
    const ADMIN = 3;
    const UNDEFINED = 4;
    const LOCAL_API = 5;
    const CLIENT_API = 6;
}

?>
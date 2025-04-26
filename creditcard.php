<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require "init.php";
$invoiceId = App::getFromRequest("invoiceid");
if(!$invoiceId) {
    App::redirect("clientarea.php");
}
App::redirectToRoutePath("invoice-pay", [$invoiceId]);

?>
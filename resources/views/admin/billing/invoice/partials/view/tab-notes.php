<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
echo $aInt->nextAdminTab();
if($invoice->adminNotes) {
    echo "<blockquote>" . nl2br($invoice->adminNotes) . "</blockquote>";
} else {
    echo WHMCS\View\Helper::alert("There are no notes on this invoice.", "info", "no-margin");
}

?>
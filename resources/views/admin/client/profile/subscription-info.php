<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
if(!empty($errorMsg)) {
    echo "    <div class=\"alert alert-danger\">\n        <strong>";
    echo AdminLang::trans("subscription.unableToRetrieve");
    echo ":</strong>\n        <br>\n        ";
    echo $errorMsg;
    echo "    </div>\n";
} else {
    echo "\n    ";
    if($isActive) {
        echo "        <div class=\"alert alert-success\">\n            <i class=\"fas fa-check fa-fw\"></i>\n            ";
        echo AdminLang::trans("subscription.active");
        echo "        </div>\n    ";
    }
    echo "\n    ";
    echo $subscriptionDetails;
    echo "\n";
}

?>
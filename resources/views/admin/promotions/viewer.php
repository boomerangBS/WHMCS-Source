<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
echo "\n<div class=\"alert alert-info alert-dismissible textblack clearfix\"\n     data-identifier=\"";
echo $promotion->getIdentifier();
echo "\"\n>\n    ";
if($promotion->isDismissible()) {
    echo "        <button type=\"button\" class=\"close\">\n            <span aria-hidden=\"true\">&times;</span>\n        </button>\n    ";
}
echo "    <div class=\"promotion-content\">\n        ";
if($promotion->getLogoUrl()) {
    echo "            <img src=\"";
    echo $promotion->getLogoUrl();
    echo "\" class=\"promotion-logo\">\n        ";
}
echo "        <div>\n            <strong class=\"promotion-title\">";
echo $promotion->getTitle();
echo "</strong>\n            <p class=\"promotion-description\">";
echo $promotion->getDescription();
echo "</p>\n        </div>\n    </div>\n    ";
if($promotion->hasAction()) {
    echo "        ";
    echo $promotion->getAction()->view();
    echo "    ";
} else {
    echo "        <div class=\"btn btn-sm invisible\">&nbsp</div>\n    ";
}
echo "</div>\n";

?>
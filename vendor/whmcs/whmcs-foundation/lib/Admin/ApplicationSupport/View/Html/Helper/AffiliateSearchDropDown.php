<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Admin\ApplicationSupport\View\Html\Helper;

class AffiliateSearchDropDown extends ClientSearchDropdown
{
    public function getFormattedHtmlHeadContent()
    {
        return "<script>function getClientSearchPostUrl() { return '" . routePath("admin-search-affiliate") . "'; }</script>" . PHP_EOL . "<script type=\"text/javascript\" " . "src=\"../assets/js/AdminClientDropdown.js\"></script>";
    }
}

?>
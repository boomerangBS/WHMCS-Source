<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Notification\Contracts;

interface NotificationAttributeInterface
{
    public function getLabel();
    public function setLabel($label);
    public function getValue();
    public function setValue($value);
    public function getUrl();
    public function setUrl($url);
    public function getStyle();
    public function setStyle($style);
    public function getIcon();
    public function setIcon($icon);
}

?>
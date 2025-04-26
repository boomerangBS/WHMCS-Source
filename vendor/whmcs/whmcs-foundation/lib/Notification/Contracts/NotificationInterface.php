<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Notification\Contracts;

interface NotificationInterface
{
    public function getTitle();
    public function setTitle($title);
    public function getMessage();
    public function setMessage($message);
    public function getUrl();
    public function setUrl($url);
    public function getAttributes();
    public function setAttributes($attributes);
    public function addAttribute(NotificationAttributeInterface $attribute);
}

?>
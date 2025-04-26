<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Contracts;

interface NotificationModuleInterface
{
    public function settings();
    public function isActive();
    public function getName();
    public function getDisplayName();
    public function getLogoPath();
    public function testConnection($settings);
    public function notificationSettings();
    public function getDynamicField($fieldName, $settings);
    public function sendNotification(\WHMCS\Notification\Contracts\NotificationInterface $notification, $moduleSettings, $notificationSettings);
}

?>
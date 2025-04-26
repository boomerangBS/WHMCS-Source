<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Admin\AdminInvites\Services;

class AdminInviteNotificationService
{
    public function sendNotification(\WHMCS\Admin\AdminInvites\Model\AdminInvite $adminInvite) : \WHMCS\Admin\AdminInvites\Model\AdminInvite
    {
        $emailer = \WHMCS\Mail\Emailer::factoryByTemplate(\WHMCS\Admin\AdminInvites\Model\AdminInvite::ADMIN_INVITATION_EMAIL_TEMPLATE, $adminInvite->getKey());
        return $emailer->send();
    }
}

?>
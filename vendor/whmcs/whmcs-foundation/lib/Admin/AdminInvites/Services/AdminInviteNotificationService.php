<?php

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
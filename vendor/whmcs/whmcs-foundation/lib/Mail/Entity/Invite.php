<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Mail\Entity;

class Invite extends \WHMCS\Mail\Emailer
{
    protected function getEntitySpecificMergeData($inviteId, $extraParams)
    {
        $invite = \WHMCS\User\User\UserInvite::find($inviteId);
        if(!$invite) {
            throw new \WHMCS\Exception("Invalid invite id provided");
        }
        $this->setRecipient(0, $invite);
        $email_merge_fields = [];
        $email_merge_fields["invite_email"] = $invite->email;
        $email_merge_fields["invite_account_name"] = $invite->getClientName();
        $email_merge_fields["invite_sender_name"] = $invite->getSenderName();
        $email_merge_fields["invite_sent_by_admin"] = $invite->invitedByAdmin;
        $email_merge_fields["invite_accept_url"] = $invite->getUrl();
        $this->massAssign($email_merge_fields);
    }
}

?>
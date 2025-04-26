<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Mail\Entity;

class AdminInvite extends \WHMCS\Mail\Emailer
{
    protected $isNonClientEmail = true;
    protected function getEntitySpecificMergeData($adminInviteId, array $extraParams) : void
    {
        $adminInvite = \WHMCS\Admin\AdminInvites\Model\AdminInvite::find($adminInviteId);
        if(!$adminInvite) {
            throw new \WHMCS\Exception("Invalid admin invite ID.");
        }
        $this->message->addRecipient("to", $adminInvite->email);
        $emailMergeFields = ["invite_email" => $adminInvite->email, "invite_accept_url" => routePathWithQuery("admin-invite-prompt", [], ["auth_token" => $adminInvite->token], true), "invite_sender_name" => $adminInvite->sender->fullName, "expiration_period_in_days" => $adminInvite->daysLeftUntilInviteExpiration()];
        $this->massAssign($emailMergeFields);
    }
}

?>
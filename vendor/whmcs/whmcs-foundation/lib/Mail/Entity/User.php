<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Mail\Entity;

class User extends \WHMCS\Mail\Emailer
{
    protected function getEntitySpecificMergeData($userId, $extraParams)
    {
        $user = \WHMCS\User\User::find($userId);
        if(!$user) {
            throw new \WHMCS\Exception("Invalid user id provided");
        }
        $this->setRecipient(0, $user);
        $email_merge_fields = [];
        $email_merge_fields["user_first_name"] = $user->firstName;
        $email_merge_fields["user_last_name"] = $user->lastName;
        $email_merge_fields["user_email"] = $user->email;
        $this->massAssign($email_merge_fields);
    }
}

?>
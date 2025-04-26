<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\User\Observers;

class UserObserver
{
    protected $updateLogging = ["First Name" => "first_name", "Last Name" => "last_name", "Email Address" => "email", "Language" => "language"];
    public function updated(\WHMCS\User\User $user) : void
    {
        $changeList = $user->getChanges();
        $changes = [];
        foreach ($this->updateLogging as $friendly => $field) {
            if(count($changeList) === 0) {
                if(0 < count($changes)) {
                    logActivity("User Account Modified - " . implode(", ", $changes) . " - UserID: " . $user->id);
                }
            } elseif(!array_key_exists($field, $changeList)) {
            } else {
                $changes[] = $friendly . " changed from '" . $user->getOriginal($field) . "'" . " to '" . $user->getAttribute($field) . "'";
                unset($changeList[$field]);
            }
        }
    }
}

?>
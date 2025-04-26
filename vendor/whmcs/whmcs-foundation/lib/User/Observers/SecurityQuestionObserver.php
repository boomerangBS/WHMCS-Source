<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\User\Observers;

class SecurityQuestionObserver
{
    public function created(\WHMCS\User\User\SecurityQuestion $question) : void
    {
        logAdminActivity("Security Question Created - Security Question ID: " . $question->id);
    }
    public function deleted(\WHMCS\User\User\SecurityQuestion $question) : void
    {
        logAdminActivity("Security Question Deleted - Security Question ID: " . $question->id);
    }
    public function updated(\WHMCS\User\User\SecurityQuestion $question) : void
    {
        $changeList = $question->getChanges();
        if(0 < count($changeList)) {
            logAdminActivity("Security Question Modified - Security Question ID: " . $question->id);
        }
    }
}

?>
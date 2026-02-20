<?php

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
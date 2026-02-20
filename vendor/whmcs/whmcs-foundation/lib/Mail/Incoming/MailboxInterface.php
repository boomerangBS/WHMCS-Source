<?php

namespace WHMCS\Mail\Incoming;

interface MailboxInterface
{
    public static function createForDepartment(\WHMCS\Support\Department $department, $isTest) : MailboxInterface;
    public function getMessageCount() : int;
    public function getAllMessages() : \Iterator;
    public function getRfcMessage($messageIndex, $messageData);
    public function deleteMessage($messageIndex) : void;
    public function close() : void;
}

?>